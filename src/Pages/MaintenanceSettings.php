<?php

namespace Albertofuentes\FilamentMaintenance\Pages;

use Albertofuentes\FilamentMaintenance\Support\MaintenanceManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read Schema $form
 */
class MaintenanceSettings extends Page
{
    protected string $view = 'filament-maintenance::pages.maintenance-settings';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance';

    protected static ?string $title = 'Maintenance';

    protected static ?string $slug = 'maintenance';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

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

        $this->form->fill([
            'enabled' => $setting->enabled,
            'maintenanceTitle' => (string) ($setting->title ?: config('filament-maintenance.default_title')),
            'message' => (string) ($setting->message ?: config('filament-maintenance.default_message')),
            'customView' => (string) ($setting->view ?: ''),
            'allowedIps' => implode(PHP_EOL, $setting->allowed_ips ?? []),
            'allowedRoles' => implode(PHP_EOL, $setting->allowed_roles ?? []),
            'managerRoles' => implode(PHP_EOL, $setting->manager_roles ?? []),
            'managerUserIds' => $setting->manager_user_ids ?? [],
        ]);
    }

    public function save(MaintenanceManager $maintenance): void
    {
        abort_unless(static::canAccess(), 403);

        $data = $this->form->getState();

        $setting = $maintenance->setting($this->panelId());
        $wasEnabled = $setting->enabled;

        $setting->forceFill([
            'enabled' => $data['enabled'],
            'title' => $data['maintenanceTitle'] ?: config('filament-maintenance.default_title'),
            'message' => $data['message'] ?: config('filament-maintenance.default_message'),
            'view' => filled(trim((string) ($data['customView'] ?? ''))) ? trim((string) $data['customView']) : null,
            'allowed_ips' => $this->lines($data['allowedIps'] ?? ''),
            'allowed_roles' => $this->lines($data['allowedRoles'] ?? ''),
            'manager_roles' => $this->lines($data['managerRoles'] ?? ''),
            'manager_user_ids' => array_values(array_filter($data['managerUserIds'] ?? [])),
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
            ->title('Configuration saved')
            ->success()
            ->send();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Panel status')
                    ->description('Enable maintenance mode only for this Filament panel.')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Maintenance mode')
                            ->inline(false),
                    ]),

                Section::make('Maintenance page')
                    ->schema([
                        TextInput::make('maintenanceTitle')
                            ->label('Title')
                            ->maxLength(255),

                        Textarea::make('message')
                            ->label('Message')
                            ->rows(5),

                        TextInput::make('customView')
                            ->label('Custom Blade view')
                            ->placeholder('filament-maintenance::maintenance.default')
                            ->helperText('Leave empty to use the package default view.')
                            ->maxLength(255),
                    ]),

                Section::make('Access during maintenance')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Textarea::make('allowedIps')
                                    ->label('Allowed IP addresses or CIDR ranges')
                                    ->placeholder("127.0.0.1\n192.168.1.0/24")
                                    ->rows(5)
                                    ->helperText('One entry per line or comma-separated. IPv4, IPv6 and CIDR ranges are supported.'),

                                Textarea::make('allowedRoles')
                                    ->label('Spatie roles that can access')
                                    ->placeholder("admin\nsupport")
                                    ->rows(3)
                                    ->helperText('Users with any of these roles can enter while maintenance mode is enabled.'),
                            ]),
                    ]),

                Section::make('Maintenance managers')
                    ->description('These users or roles can open this page and use the header switch.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('managerUserIds')
                                    ->label('Manager users')
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn (): array => $this->getAvailableUsersProperty())
                                    ->helperText('If no users or roles are configured, authenticated users can manage the first setup.'),

                                Textarea::make('managerRoles')
                                    ->label('Spatie manager roles')
                                    ->placeholder('admin')
                                    ->rows(4),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('maintenance-settings-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save configuration')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function getHeading(): string | Htmlable
    {
        return 'Maintenance panel';
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
