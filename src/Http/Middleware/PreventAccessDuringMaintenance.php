<?php

namespace Albertofuentes\FilamentMaintenance\Http\Middleware;

use Albertofuentes\FilamentMaintenance\Support\MaintenanceManager;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessDuringMaintenance
{
    public function __construct(
        private readonly MaintenanceManager $maintenance,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();
        $panelId = $panel?->getId();

        if (blank($panelId) || $this->maintenance->canBypass($request, $panelId)) {
            return $next($request);
        }

        $setting = $this->maintenance->setting($panelId);

        return response()
            ->view($setting->view ?: config('maintenance.view'), [
                'panelId' => $panelId,
                'setting' => $setting,
                'title' => $setting->title ?: config('maintenance.default_title'),
                'message' => $setting->message ?: config('maintenance.default_message'),
            ], 503)
            ->header('Retry-After', '3600');
    }
}
