<?php

namespace App\Enums;

enum SentStatusEnum: string
{
    case SENT='yes';
    case NOT_SENT='no';
    case PENDING='pending';
}
