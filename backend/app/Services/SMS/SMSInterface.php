<?php

namespace App\Services\SMS;

use App\Enums\SmsTypeEnum;

interface SMSInterface
{
    /**
     * Send message
     * @param array $mobiles
     * @param string $msg
     * @param SmsTypeEnum $type
     * @return bool
     */
    public function sendMessage(array $mobiles, string $msg, SmsTypeEnum $type):bool;
}
