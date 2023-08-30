<?php

namespace App\Services\SMS;

use App\Enums\SmsTypeEnum;
use Illuminate\Support\Facades\DB;

class RayganSms extends SmsService implements SmsInterface
{

    /**
     * Send message
     * @param array $mobiles
     * @param string $msg
     * @param SmsTypeEnum $type
     * @return bool
     */
    public function sendMessage(array $mobiles, string $msg, SmsTypeEnum $type): bool
    {

        DB::beginTransaction();
        try{
            if($type == SmsTypeEnum::CODE){
                self::delete($mobiles[0], $type);
            }
            self::store($mobiles, $msg, $type);
            foreach ($mobiles as $mobile){
                \Trez\RayganSms\Facades\RayganSms::sendAuthCode($mobile, $msg, false);
            }
            return true;
        }catch(\Exception $e){
            return false;
        }

    }
}
