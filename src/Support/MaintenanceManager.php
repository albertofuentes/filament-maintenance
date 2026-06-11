<?php

namespace Albertofuentes\FilamentMaintenance\Support;

use Albertofuentes\FilamentMaintenance\Models\MaintenanceEvent;
use Albertofuentes\FilamentMaintenance\Models\MaintenanceSetting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class MaintenanceManager
{
    public function __construct(
        private readonly IpMatcher $ipMatcher,
    ) {}

    public function setting(string $panelId): MaintenanceSetting
    {
        /** @var class-string<MaintenanceSetting> $model */
        $model = config('maintenance.setting_model', MaintenanceSetting::class);

        return $model::query()->firstOrCreate(
            ['panel_id' => $panelId],
            [
                'title' => config('maintenance.default_title'),
                'message' => config('maintenance.default_message'),
            ],
        );
    }

    public function isEnabled(string $panelId): bool
    {
        return $this->setting($panelId)->enabled;
    }

    public function canManage(Authenticatable $user, string $panelId): bool
    {
        $callback = config('maintenance.can_manage_using');

        if (is_callable($callback) && $callback($user, $panelId) === true) {
            return true;
        }

        $setting = $this->setting($panelId);

        if (method_exists($user, 'can') && $user->can('manage-filament-maintenance')) {
            return true;
        }

        if (($setting->manager_user_ids ?? []) === [] && ($setting->manager_roles ?? []) === []) {
            return true;
        }

        return $this->userIdIsAllowed($user, $setting->manager_user_ids ?? [])
            || $this->userHasAnyRole($user, $setting->manager_roles ?? []);
    }

    public function canBypass(Request $request, string $panelId): bool
    {
        $setting = $this->setting($panelId);

        if (! $setting->enabled) {
            return true;
        }

        if ($this->ipMatcher->matches($request->ip(), $setting->allowed_ips ?? [])) {
            return true;
        }

        $user = $request->user();

        if (! $user instanceof Authenticatable) {
            return false;
        }

        $callback = config('maintenance.can_bypass_using');

        if (is_callable($callback) && $callback($user, $panelId) === true) {
            return true;
        }

        return $this->canManage($user, $panelId)
            || $this->userHasAnyRole($user, $setting->allowed_roles ?? []);
    }

    public function enable(string $panelId, ?Authenticatable $user = null, ?string $ip = null): MaintenanceSetting
    {
        $setting = $this->setting($panelId);

        $setting->forceFill([
            'enabled' => true,
            'enabled_by' => $this->userKey($user),
            'enabled_at' => now(),
        ])->save();

        $this->record($panelId, 'enabled', $user, $ip);

        return $setting;
    }

    public function disable(string $panelId, ?Authenticatable $user = null, ?string $ip = null): MaintenanceSetting
    {
        $setting = $this->setting($panelId);

        $setting->forceFill([
            'enabled' => false,
            'disabled_by' => $this->userKey($user),
            'disabled_at' => now(),
        ])->save();

        $this->record($panelId, 'disabled', $user, $ip);

        return $setting;
    }

    public function record(string $panelId, string $action, ?Authenticatable $user = null, ?string $ip = null, array $payload = []): MaintenanceEvent
    {
        /** @var class-string<MaintenanceEvent> $model */
        $model = config('maintenance.event_model', MaintenanceEvent::class);

        return $model::query()->create([
            'panel_id' => $panelId,
            'action' => $action,
            'user_id' => $this->userKey($user),
            'ip' => $ip,
            'payload' => $payload,
        ]);
    }

    private function userIdIsAllowed(Authenticatable $user, array $allowedUserIds): bool
    {
        return in_array((string) $user->getAuthIdentifier(), array_map('strval', $allowedUserIds), true);
    }

    private function userHasAnyRole(Authenticatable $user, array $roles): bool
    {
        $roles = array_values(array_filter(array_map('strval', $roles)));

        if ($roles === [] || ! method_exists($user, 'hasAnyRole')) {
            return false;
        }

        return (bool) $user->hasAnyRole($roles);
    }

    private function userKey(?Authenticatable $user): int | string | null
    {
        return $user?->getAuthIdentifier();
    }
}
