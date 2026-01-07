<?php

namespace Chargily\ChargilyProLaravel\Exceptions;

class VoucherOutOfStockException extends ChargilyProException
{
    public function __construct(string $name)
    {
        parent::__construct("Voucher '{$name}' is out of stock .");
    }
}
