<?php

namespace Chargily\ChargilyProLaravel\Exceptions;

class ModeNotFoundException extends ChargilyProException
{
    public function __construct(string $name)
    {
        parent::__construct("Mode '{$name}' not found.");
    }
}
