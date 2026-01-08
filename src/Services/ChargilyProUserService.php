<?php

namespace Chargily\ChargilyProLaravel\Services;

use Chargily\ChargilyPro\Auth\Credentials;
use Chargily\ChargilyPro\ChargilyPro;

class ChargilyProUserService
{
    /**
     * Chargily pro instance.
     *
     * @var ChargilyPro
     */
    protected ChargilyPro $chargily;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->chargily = new ChargilyPro(new Credentials(config("chargily-pro.credentials")));
    }
    /**
     * Get user balance
     *
     * @return \Chargily\ChargilyPro\Elements\UserBalanceElement|null
     */
    public function balance()
    {
        return $this->chargily->user()->balance()->get();
    }
}
