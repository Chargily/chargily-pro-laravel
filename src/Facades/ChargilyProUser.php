<?php

namespace Chargily\ChargilyProLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Chargily\ChargilyPro\Elements\UserBalanceElement balance()
 *
 * @see Chargily\ChargilyProLaravel\Services\ChargilyProUserService
 */
class ChargilyProUser extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'chargily-pro-user-service';
    }
}
