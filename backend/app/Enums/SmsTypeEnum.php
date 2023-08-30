<?php

namespace App\Enums;

enum SmsTypeEnum :String
{
    case CODE = "code";
    case MESSAGE = "message";
}
