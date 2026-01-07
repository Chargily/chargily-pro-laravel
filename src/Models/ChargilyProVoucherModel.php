<?php

namespace Chargily\ChargilyProLaravel\Models;

use Chargily\ChargilyProLaravel\Enums\ChargilyProVoucherStatusEnum;
use Illuminate\Database\Eloquent\Model;

class ChargilyProVoucherModel extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = "chargily_pro_vouchers";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "value",
        "quantity",
        "status",
        "serial",
        "key",
        "message",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "status" => ChargilyProVoucherStatusEnum::class,
    ];
}
