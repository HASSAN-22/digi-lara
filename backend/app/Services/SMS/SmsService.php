<?php

namespace App\Services\SMS;

use App\Enums\SentStatusEnum;
use App\Enums\SmsTypeEnum;
use App\Models\Sms;

class SmsService
{
    /**
     * Delete sms record
     * @param string $mobile
     * @param SmsTypeEnum $type
     * @return mixed
     */
    public static function delete(string $mobile, SmsTypeEnum $type){
        return Sms::where('mobile',$mobile)->where('type',$type)->delete();
    }

    /**
     * Store a sms
     * @param array $mobiles
     * @param string $msg
     * @param SmsTypeEnum $type
     * @return void
     */
    public static function store(array $mobiles, string $msg, SmsTypeEnum $type){
        $data = [];
        $userId = auth()->Id();
        foreach($mobiles as $mobile){
            $data[] = [
                'mobile'=>$mobile,
                'message'=>$msg,
                'type'=>$type,
                'created_at'=>now(),
                'updated_at'=>now()
            ];
        }
        Sms::insert($data);
    }

    /**
     * Checks if there is a SMS
     * @param string $msg
     * @return mixed
     */
    public static function existsMessage(string $msg){
        return Sms::where('message',$msg)->first();
    }

    /**
     * Checking for the correctness of the code, this item is used for things like `Register, Login ...`
     * @param string $mobile
     * @param string $code
     * @return bool
     */
    public function codeIsValid(string $mobile, string $code): bool
    {

        return !is_null(Sms::where(['message'=>$code, 'mobile'=>$mobile])->where('created_at','>', now()->subMinutes(15))->first());
    }

    /**
     * Generate random code number
     * @return string|void
     */
    public static function generateCode(){
        $numbers = '0123456789';
        $code = substr(str_shuffle($numbers), 0, 6);
        if(self::existsMessage($code)){
            self::generateCode();
        }else{
            return $code;
        }
    }
}
