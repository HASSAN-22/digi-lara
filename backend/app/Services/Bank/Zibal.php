<?php

namespace App\Services\Bank;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;

class Zibal implements Gateway {

    private static string $BASE_PATCH = "https://gateway.zibal.ir/v1/";

    /**
     * Send the transaction to the bank portal
     * @param array $gatewayInfo
     * @return string
     * @throws Exception
     */
    public static function request(array $gatewayInfo): string
    {
        $data = [
            "merchant" => $gatewayInfo['merchantId'],
            "amount" => $gatewayInfo['amount'],
            "mobile"=> $gatewayInfo['mobile'],
            "callbackUrl" => $gatewayInfo['callback'],
            "orderId"=> $gatewayInfo['orderId'],
            "percentMode"=> 0,
            "feeMode"=> 0,
            "description" => $gatewayInfo['description'] ?? '',
            "multiplexingInfos" => $gatewayInfo['metadata'] ?? [],
        ];
        $response = self::executeCurl('request', $data);
        if ($response['result'] == 100)
        {
            return "https://gateway.zibal.ir/start/".$response['trackId'];
        }else{
            throw new Exception(self::statusCode($response['result']), 500);
        }
    }


    /**
     * Checking the transaction result
     * @param array $responseInfo
     * @return array
     * @throws Exception
     */
    public static function verify(array $responseInfo): array
    {
        $data = [
            "merchant" => $responseInfo['merchantId'],
            "trackId" => $responseInfo['trackId'],
        ];
        $response = self::executeCurl('verify', $data);
        if ($response['result'] == 100) {
            return ['status'=>'success','data'=>$response];
        } else {
            throw new Exception(self::statusCode($response['result']), 500);
        }
    }

    /**
     * Call rest api
     * @param string $path
     * @param array $data
     * @return array
     */
    private static function executeCurl(string $path, array $data):array{
        $url = self::$BASE_PATCH.$path;
        $response = Http::withOptions(['verify' => false])->post($url, $data);
        return json_decode($response->body(), true, JSON_PRETTY_PRINT);
    }

    /**
     * Checking status code
     * @param string $code
     * @return string
     */
    private static function statusCode(string $code): string{
        return match($code){
            '100'=>'با موفقیت تایید شد',
            '102'=>'merchant id یافت نشد',
            '103'=>'merchant id غیرفعال',
            '104'=>'merchant id نامعتبر',
            '201'=>'قبلا تایید شده',
            '105'=>'amount بایستی بزرگتر از 1,000 ریال باشد',
            '106'=>'callbackUrl نامعتبر می‌باشد. (شروع با http و یا https)',
            '113'=>'amount مبلغ تراکنش از سقف میزان تراکنش بیشتر است.',
            '202'=>'سفارش پرداخت نشده یا ناموفق بوده است',
            '203'=>'trackId نامعتبر می‌باشد',
            default=>'وضعیت مشخص شده معتبر نیست'
        };
    }

}
