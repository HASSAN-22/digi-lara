<?php

namespace App\Enums;

enum DebtorStatusEnum: string
{
    case SETTLED='settled';
    case PENDING='pending';
}
