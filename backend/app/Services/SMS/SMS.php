<?php

namespace App\Services\SMS;

use App\Enums\SmsTypeEnum;
use Illuminate\Support\Facades\Log;

class SMS
{
    private static SMSInterface $driver;

    /**
     * Send message
     * @param array $mobiles
     * @param string $msg
     * @param SmsTypeEnum $type
     * @return bool
     */
    public static function sendMessage(array $mobiles, string $msg, SmsTypeEnum $type):bool{
        return self::$driver->sendMessage($mobiles,$msg, $type);
    }

    /**
     * Choose sms driver
     * @param string $driver
     * @return static
     * @throws \Exception
     */
    public static function driver(string $driver)
    {
        $driver = "App\Services\SMS\\".$driver;
        self::$driver = new $driver();
        if(!self::$driver instanceof SMSInterface){
            throw new \Exception("Driver `$driver` is not exist", 500);
        }
        return new static();
    }

    /**
     * Checking for the correctness of the code, this item is used for things like `Register, Login ...`
     * @param string $mobile
     * @param string $code
     * @return bool
     */
    public static function codeIsValid(string $mobile, string $code): bool
    {
        return self::$driver->codeIsValid($mobile,$code);
    }
}
