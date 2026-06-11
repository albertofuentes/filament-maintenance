<?php

namespace Albertofuentes\FilamentMaintenance\Support;

class IpMatcher
{
    public function matches(?string $ip, array $allowed): bool
    {
        if (blank($ip)) {
            return false;
        }

        foreach ($allowed as $entry) {
            $entry = trim((string) $entry);

            if ($entry === '') {
                continue;
            }

            if ($entry === $ip || $this->matchesCidr($ip, $entry)) {
                return true;
            }
        }

        return false;
    }

    private function matchesCidr(string $ip, string $cidr): bool
    {
        if (! str_contains($cidr, '/')) {
            return false;
        }

        [$subnet, $bits] = explode('/', $cidr, 2);
        $bits = filter_var($bits, FILTER_VALIDATE_INT);

        if ($bits === false) {
            return false;
        }

        $ipBinary = @inet_pton($ip);
        $subnetBinary = @inet_pton($subnet);

        if ($ipBinary === false || $subnetBinary === false || strlen($ipBinary) !== strlen($subnetBinary)) {
            return false;
        }

        $bytes = intdiv($bits, 8);
        $remainder = $bits % 8;

        if ($bytes > 0 && substr($ipBinary, 0, $bytes) !== substr($subnetBinary, 0, $bytes)) {
            return false;
        }

        if ($remainder === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remainder)) & 0xFF;

        return (ord($ipBinary[$bytes]) & $mask) === (ord($subnetBinary[$bytes]) & $mask);
    }
}
