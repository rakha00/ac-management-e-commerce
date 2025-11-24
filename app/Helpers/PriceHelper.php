<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class PriceHelper
{
    public const MODE_ECOMMERCE = 'ecommerce';
    public const MODE_RETAIL = 'retail';
    public const MODE_DEALER = 'dealer';

    public static function getMode(): string
    {
        $segment = Request::segment(1);

        if ($segment === 'retail') {
            return self::MODE_RETAIL;
        }

        if ($segment === 'dealer') {
            return self::MODE_DEALER;
        }

        // Handle Livewire requests
        if ($segment === 'livewire') {
            $referer = Request::header('referer');
            if ($referer) {
                $path = parse_url($referer, PHP_URL_PATH);
                $path = ltrim($path, '/');
                $segments = explode('/', $path);
                $firstSegment = $segments[0] ?? '';

                if ($firstSegment === 'retail') {
                    return self::MODE_RETAIL;
                }
                if ($firstSegment === 'dealer') {
                    return self::MODE_DEALER;
                }
            }
        }

        return self::MODE_ECOMMERCE;
    }

    public static function getPriceColumn(): string
    {
        return match (self::getMode()) {
            self::MODE_RETAIL => 'harga_retail',
            self::MODE_DEALER => 'harga_dealer',
            default => 'harga_ecommerce',
        };
    }

    public static function url(string $path): string
    {
        $mode = self::getMode();
        $path = ltrim($path, '/');

        if ($mode === self::MODE_ECOMMERCE) {
            return url($path);
        }

        return url($mode . '/' . $path);
    }
}
