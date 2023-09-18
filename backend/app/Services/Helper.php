<?php

use App\Enums\PaidByEnum;
use App\Enums\StatusEnum;
use App\Models\Shopconfig;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

/**
 * Convert english date to persian
 */
if(! function_exists('dateToPersian')){
    function dateToPersian(string $date): string
    {
        return Jalalian::forge(dateConvertNumbers($date))->format('Y-m-d');
    }
}

/**
 * Convert English number to Farsi or vice versa
 */
if(! function_exists('dateConvertNumbers')){
    function dateConvertNumbers(string $date, bool $englishNumber=true): string
    {
        return $englishNumber ? CalendarUtils::convertNumbers($date, true) : CalendarUtils::convertNumbers($date);
    }
}


/**
 * Get client device name
 */
if(! function_exists('getDevice')){
    function getDevice(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $userAgent = explode(') ',$userAgent);
        return (count($userAgent) > 1) ? explode(') ',explode(' (',$userAgent[0])[1])[0] : $userAgent[0];
    }
}

/**
 * Make a helper for type service
 */
if(! function_exists('typeService')){
    function typeService(string|null $date): \App\Services\TypeService
    {
        return new \App\Services\TypeService($date);
    }
}

/**
 * Make slug
 */
if(! function_exists('slug')){
    function slug(string $data, string $delimiter='-'):string{
        return str_replace(' ',$delimiter, $data);
    }
}

/**
 * Remove a file from storage
 */
if(! function_exists('removeFile')){
    function removeFile(string|null $imagePath):void{
        if(!is_null($imagePath)){
            $imagePath = str_replace('storage/','/app/uploader/', $imagePath);
            if(file_exists(storage_path($imagePath))){
                unlink(storage_path($imagePath));
            }
        }
    }
}

/**
 * Crate paginate
 */
if(! function_exists('paginate')){

    function paginate($request, $items,$limit=10):LengthAwarePaginator
    {
        $total = count($items);
        $page = $request->page ?? 1;
        $perPage = (int) $limit;
        $offset = (($request->page-1) * $perPage);
        $items = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query()
        ]);
    }
}


/**
 * Calculate percent of amount
 */
if(! function_exists('getDiscount')){
    function getDiscount($price, $discount, $numberFormat=true)
    {
        $price = $price - (($discount / 100) * $price);
        return $numberFormat ? number_format($price) : $price;
    }
}

/**
 * Set value in .env
 */
if(! function_exists('setEnvironmentValue')){
    function setEnvironmentValue(array $envValues){
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
//        $str .= "\r\n";
        if (count($envValues) > 0) {
            foreach ($envValues as $envKey => $envValue) {

                $keyPosition = strpos($str, "$envKey=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                if (is_bool($keyPosition) && $keyPosition === false) {
                    $str .= "$envKey=$envValue";
//                    $str .= "\r\n";
                } else {
                    $str = str_replace($oldLine, "$envKey=$envValue", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) {
            return false;
        }
        app()->loadEnvironmentFrom($envFile);
        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call("config:cache");
        }
    }
}



/**
 * Get shop config
 */
if(! function_exists('getShopConfig')){
    function getShopConfig()
    {
        $storeDetail = json_decode(Shopconfig::getValue('store_detail')->value??"[]");
        $footerBox = json_decode(Shopconfig::getValue('footer_box')->value??"[]");
        $socialMedia = json_decode(Shopconfig::getValue('social_media')->value??"[]");
        return ['store_detail'=>$storeDetail,'footer_box'=>$footerBox,'social_media'=>$socialMedia];
    }
}

/**
 * Set gateway info
 */
if(! function_exists('setGateway')){
    function setGateway(int $amount, int $orderId, string $mobile, string $action)
    {
        return [
            "merchantId"=>config('services.gateway.merchant_id'),
            "amount"=>$amount,
            "callback"=>route('transaction.verify',['action'=>$action]),
            "orderId"=>$orderId,
            "mobile"=>$mobile,
        ];
    }
}

/**
 * Insert a transaction for user
 */
if(! function_exists('insertTransaction')){
    function insertTransaction($post, int $userId, int $amount, int|string $refId, PaidByEnum $paidBy = PaidByEnum::USER)
    {
        $post->transactions()->create([
            'user_id'=>$userId,
            'amount'=>$amount,
            'ref_id'=>$refId,
            'status'=>StatusEnum::ACTIVE,
            'paid_by'=>$paidBy,
        ]);
    }
}
