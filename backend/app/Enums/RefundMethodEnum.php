<?php

namespace App\Enums;

enum RefundMethodEnum: string
{
    case WALLET='wallet';
    case BANK='bank';
}
