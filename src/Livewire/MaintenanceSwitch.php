<?php

namespace Albertofuentes\FilamentMaintenance\Livewire;

use Albertofuentes\FilamentMaintenance\Support\MaintenanceManager;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MaintenanceSwitch extends Component
{
    public bool $enabled = false;

    public string $panelId = '';

    public bool $canManage = false;

    public function mount(MaintenanceManager $maintenance): void
    {
        $this->panelId = (string) Filament::getCurrentPanel()?->getId();

        $user = Auth::user();

        if ($this->panelId === '' || ! $user instanceof Authenticatable) {
            return;
        }

        $this->canManage = $maintenance->canManage($user, $this->panelId);
        $this->enabled = $maintenance->isEnabled($this->panelId);
    }

    public function toggle(MaintenanceManager $maintenance): void
    {
        if (! $this->canManage || $this->panelId === '') {
            abort(403);
        }

        $this->enabled = ! $this->enabled;

        if ($this->enabled) {
            $maintenance->enable($this->panelId, Auth::user(), request()->ip());
        } else {
            $maintenance->disable($this->panelId, Auth::user(), request()->ip());
        }

        Notification::make()
            ->title($this->enabled ? 'Mantenimiento activado' : 'Mantenimiento desactivado')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('filament-maintenance::livewire.maintenance-switch');
    }
}
