<?php

namespace App\Enums;

enum WithdrawStatusEnum: string
{
    case SUCCESS='success';
    case CANCEL='cancel';
    case PENDING='pending';
}
