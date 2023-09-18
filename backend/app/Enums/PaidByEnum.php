<?php

namespace App\Enums;

enum PaidByEnum: string
{
    case ADMIN='admin';

    case SYSTEM='system';
    case USER='user';
}
