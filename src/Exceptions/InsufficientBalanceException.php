<?php

namespace Chargily\ChargilyProLaravel\Exceptions;

class InsufficientBalanceException extends ChargilyProException
{
    public function __construct()
    {
        parent::__construct("Insufficient funds to complete the order.");
    }
}
