<?php

namespace Chargily\ChargilyProLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Chargily\ChargilyPro\Elements\TopUpElement getRequest(string $id)
 * @method static \Illuminate\Database\Eloquent\Model request(string $name, string $value)
 * @method static \Illuminate\Database\Eloquent\Model requestById(string $name, string $value)
 * @method static \Chargily\ChargilyPro\Core\Helpers\Collection operators()
 * @method static \Chargily\ChargilyPro\Elements\OperatorElement getOperator(string $id)
 * @method static \Chargily\ChargilyPro\Core\Helpers\Collection modes()
 * @method static \Chargily\ChargilyPro\Elements\ModeElement getMode(string $id)
 *
 * @see Chargily\ChargilyProLaravel\Services\ChargilyProTopUpService
 */
class ChargilyProTopUp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'chargily-pro-topup-service';
    }
}
