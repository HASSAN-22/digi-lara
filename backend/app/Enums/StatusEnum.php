<?php

namespace App\Enums;

enum StatusEnum: string
{
    case ACTIVE='yes';
    case DEACTIVATED='no';
    case PENDING='pending';
}
