<?php

namespace Albertofuentes\FilamentMaintenance\Pages;

use Albertofuentes\FilamentMaintenance\Support\MaintenanceManager;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MaintenanceSettings extends Page
{
    protected string $view = 'filament-maintenance::pages.maintenance-settings';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Mantenimiento';

    protected static ?string $title = 'Mantenimiento';

    protected static ?string $slug = 'maintenance';

    public bool $enabled = false;

    public string $maintenanceTitle = '';

    public string $message = '';

    public string $customView = '';

    public string $allowedIps = '';

    public string $allowedRoles = '';

    public string $managerRoles = '';

    /** @var array<int|string> */
    public array $managerUserIds = [];

    public static function canAccess(): bool
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        $user = Auth::user();

        if (blank($panelId) || $user === null) {
            return false;
        }

        return app(MaintenanceManager::class)->canManage($user, $panelId);
    }

    public function mount(MaintenanceManager $maintenance): void
    {
        abort_unless(static::canAccess(), 403);

        $setting = $maintenance->setting($this->panelId());

        $this->enabled = $setting->enabled;
        $this->maintenanceTitle = (string) ($setting->title ?: config('maintenance.default_title'));
        $this->message = (string) ($setting->message ?: config('maintenance.default_message'));
        $this->customView = (string) ($setting->view ?: '');
        $this->allowedIps = implode(PHP_EOL, $setting->allowed_ips ?? []);
        $this->allowedRoles = implode(PHP_EOL, $setting->allowed_roles ?? []);
        $this->managerRoles = implode(PHP_EOL, $setting->manager_roles ?? []);
        $this->managerUserIds = $setting->manager_user_ids ?? [];
    }

    public function save(MaintenanceManager $maintenance): void
    {
        abort_unless(static::canAccess(), 403);

        $validated = $this->validate([
            'enabled' => ['boolean'],
            'maintenanceTitle' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'customView' => ['nullable', 'string', 'max:255'],
            'allowedIps' => ['nullable', 'string'],
            'allowedRoles' => ['nullable', 'string'],
            'managerRoles' => ['nullable', 'string'],
            'managerUserIds' => ['array'],
        ]);

        $setting = $maintenance->setting($this->panelId());
        $wasEnabled = $setting->enabled;

        $setting->forceFill([
            'enabled' => $validated['enabled'],
            'title' => $validated['maintenanceTitle'] ?: config('maintenance.default_title'),
            'message' => $validated['message'] ?: config('maintenance.default_message'),
            'view' => $validated['customView'] ?: null,
            'allowed_ips' => $this->lines($validated['allowedIps'] ?? ''),
            'allowed_roles' => $this->lines($validated['allowedRoles'] ?? ''),
            'manager_roles' => $this->lines($validated['managerRoles'] ?? ''),
            'manager_user_ids' => array_values(array_filter($validated['managerUserIds'] ?? [])),
        ])->save();

        $action = match (true) {
            ! $wasEnabled && $setting->enabled => 'enabled',
            $wasEnabled && ! $setting->enabled => 'disabled',
            default => 'updated',
        };

        $maintenance->record($this->panelId(), $action, Auth::user(), request()->ip(), [
            'changed_from_settings_page' => true,
        ]);

        Notification::make()
            ->title('Configuracion guardada')
            ->success()
            ->send();
    }

    public function getHeading(): string | Htmlable
    {
        return 'Mantenimiento del panel';
    }

    /**
     * @return array<int|string, string>
     */
    public function getAvailableUsersProperty(): array
    {
        $model = config('auth.providers.users.model');

        if (! is_string($model) || ! is_subclass_of($model, Model::class)) {
            return [];
        }

        try {
            return $model::query()
                ->limit(100)
                ->get()
                ->mapWithKeys(fn (Model $user): array => [
                    $user->getKey() => $this->userLabel($user),
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function panelId(): string
    {
        return (string) Filament::getCurrentPanel()?->getId();
    }

    /**
     * @return array<int, string>
     */
    private function lines(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function userLabel(Model $user): string
    {
        foreach (['name', 'email'] as $attribute) {
            if (filled($user->getAttribute($attribute))) {
                return (string) $user->getAttribute($attribute);
            }
        }

        return (string) $user->getKey();
    }
}
