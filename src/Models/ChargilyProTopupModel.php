<?php

namespace Chargily\ChargilyProLaravel\Models;

use Chargily\ChargilyProLaravel\Enums\ChargilyProTopupStatusEnum;
use Illuminate\Database\Eloquent\Model;

class ChargilyProTopupModel extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = "chargily_pro_topups";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "operator",
        "mode_name",
        "value",
        "country_code",
        "phone_number",
        "status",
        "message"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        "status" => ChargilyProTopupStatusEnum::class,
    ];
}
