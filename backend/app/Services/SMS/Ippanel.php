<?php

namespace App\Services\SMS;

use App\Enums\SmsTypeEnum;
use App\Events\IppanelEvent;
use Illuminate\Support\Facades\DB;

class Ippanel extends SmsService implements SmsInterface
{

    /**
     * Send message
     * @param array $mobiles
     * @param string $msg
     * @param SmsTypeEnum $type
     * @return bool
     */
    public function sendMessage(array $mobiles, string $msg, SmsTypeEnum $type):bool
    {
        DB::beginTransaction();
        try{
            if($type == SmsTypeEnum::CODE){
                self::delete($mobiles[0], $type);
            }
            self::store($mobiles, $msg, $type);
            event(new IppanelEvent($mobiles, $msg));
            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollBack();
            return false;
        }
    }

}
