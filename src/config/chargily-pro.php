<?php

use Chargily\ChargilyProLaravel\Models\ChargilyProTopupModel;
use Chargily\ChargilyProLaravel\Models\ChargilyProVoucherModel;

return [
    /**
     * ======================
     *  API credentials     =
     * ======================
     */
    "credentials" => [
        /**
         * Must be live or test
         */
        "mode" => env("CHARGILY_PRO_MODE", null),
        /**
         * You API username
         */
        "name" => env("CHARGILY_PRO_NAME", null),
        /**
         * Your API public key
         */
        "public" => env("CHARGILY_PRO_PUBLIC_KEY", null),
        /**
         * Your API secret key
         */
        "secret" => env("CHARGILY_PRO_SECRET_KEY", null),
    ],
    /**
     * =====================
     *  Database models    =
     * =====================
     */
    "models" => [
        /**
         * TopUps model
         */
        "topups" => ChargilyProTopupModel::class,
        /**
         * Vouchers model
         */
        "vouchers" => ChargilyProVoucherModel::class,
    ],
    /**
     * ===============
     *  Routes       =
     * ===============
     */
    "routes" => [
        /**
         * TopUps webhook route name.
         */
        "topup-webhook" => "chargily-pro.api.topup-webhook",
    ],
];
