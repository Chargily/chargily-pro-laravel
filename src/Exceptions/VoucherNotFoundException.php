<?php

namespace Chargily\ChargilyProLaravel\Exceptions;

class VoucherNotFoundException extends ChargilyProException
{
    public function __construct(string $name)
    {
        parent::__construct("Voucher '{$name}' not found.");
    }
}
