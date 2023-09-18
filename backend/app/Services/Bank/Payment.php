<?php


namespace App\Services\Bank;

class Payment{

    private static Gateway $driver;

    /**
     * Choose sms driver
     * @param string $driver
     * @return Payment
     * @throws \Exception
     */
    public static function driver(string $driver): Payment
    {
        $driver = "App\Services\Bank\\".$driver;
        self::$driver = new $driver();
        if(!self::$driver instanceof Gateway){
            throw new \Exception("Driver `$driver` is not exist", 500);
        }
        return new static();
    }

    /**
     *
     * Send the transaction to the bank portal
     * @param array $gatewayInfo
     * @return string
     * @throws \Exception
     */
	public static function request(array $gatewayInfo): string
    {
		return self::$driver->request($gatewayInfo);
	}


    /**
     *
     * Checking the transaction result
     * @param array $responseInfo
     * @return array
     * @throws \Exception
     */
	public static function verify(array $responseInfo): array
    {
		return self::$driver->verify($responseInfo);
	}
}
