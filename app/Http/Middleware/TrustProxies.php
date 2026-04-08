<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class TrustProxies extends Middleware
{
    protected $proxies;

    public function __construct()
    {
        $this->proxies = $this->resolveTrustedProxies();
    }

    private function resolveTrustedProxies(): array|string
    {
        $trustedProxies = (string) env('TRUSTED_PROXIES', '*');

        if ($trustedProxies === '*' || $trustedProxies === '') {
            return '*';
        }

        return array_values(array_filter(array_map('trim', explode(',', $trustedProxies))));
    }

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
