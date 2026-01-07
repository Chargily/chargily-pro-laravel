<?php

namespace Chargily\ChargilyProLaravel\Enums;

enum ChargilyProVoucherStatusEnum: int
{
    case CREATED = 1;
    case PROCESSING = 2;
    case COMPLETED = 3;
    case FAILED = 4;
    /**
     * Label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Created',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }
}
