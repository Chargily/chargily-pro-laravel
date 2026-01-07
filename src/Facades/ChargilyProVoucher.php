<?php

namespace Chargily\ChargilyProLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Chargily\ChargilyPro\Elements\VoucherElement|null get(string $id)
 * @method static Chargily\ChargilyPro\Core\Helpers\Collection|null all()
 * @method static Chargily\ChargilyPro\Core\Helpers\Collection|null sold()
 * @method static \Illuminate\Database\Eloquent\Model|null request(string $name, string $value)
 * @method static \Illuminate\Database\Eloquent\Model|null requestById(string|\Chargily\ChargilyPro\Elements\VoucherElement $id)
 *
 * @see Chargily\ChargilyProLaravel\Services\ChargilyProVoucherService
 */
class ChargilyProVoucher extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'chargily-pro-voucher-service';
    }
}
