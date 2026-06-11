<?php

use Albertofuentes\FilamentMaintenance\Support\IpMatcher;

it('matches exact ips', function () {
    expect((new IpMatcher)->matches('127.0.0.1', ['127.0.0.1']))->toBeTrue();
});

it('matches ipv4 cidr ranges', function () {
    expect((new IpMatcher)->matches('192.168.1.25', ['192.168.1.0/24']))->toBeTrue()
        ->and((new IpMatcher)->matches('192.168.2.25', ['192.168.1.0/24']))->toBeFalse();
});

it('matches ipv6 cidr ranges', function () {
    expect((new IpMatcher)->matches('2001:db8::1', ['2001:db8::/32']))->toBeTrue()
        ->and((new IpMatcher)->matches('2001:db9::1', ['2001:db8::/32']))->toBeFalse();
});
