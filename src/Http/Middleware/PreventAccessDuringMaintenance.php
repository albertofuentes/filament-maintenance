<?php

namespace Albertofuentes\FilamentMaintenance\Http\Middleware;

use Albertofuentes\FilamentMaintenance\Support\MaintenanceManager;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessDuringMaintenance
{
    private const DEFAULT_VIEW = 'filament-maintenance::maintenance.default';

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
            ->view($this->viewName($setting->view), [
                'panelId' => $panelId,
                'setting' => $setting,
                'title' => $setting->title ?: $this->configString('default_title', 'Panel under maintenance'),
                'message' => $setting->message ?: $this->configString('default_message', 'We are performing maintenance. Please try again later.'),
            ], 503)
            ->header('Retry-After', '3600');
    }

    private function viewName(mixed $view): string
    {
        if (is_string($view) && filled(trim($view))) {
            return trim($view);
        }

        return $this->configString('view', self::DEFAULT_VIEW);
    }

    private function configString(string $key, string $default): string
    {
        $value = config("filament-maintenance.{$key}");

        if (is_string($value) && filled(trim($value))) {
            return trim($value);
        }

        return $default;
    }
}
