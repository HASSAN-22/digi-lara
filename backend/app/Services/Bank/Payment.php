<?php


namespace App\Services\Bank;

class Payment{

    private static Gateway $driver;

    /**
     * Choose sms driver
     * @param string $driver
     * @return static
     * @throws \Exception
     */
    public static function driver(string $driver)
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
    * *******************************
    ###	To send information to ``Zarinpal`` portal, send the gateway information like this
    ```php
    array[
        amount=>type(int),
        merchantId=>type(str),
        callback=>type(str),
        (optional)description=>type(str),
        (optional)metadata=>type(array)
    ]
    ```
    *******************************
    ###	To send information to ``Melat`` portal, send gateway Info like this
    ```php
    array[
        terminalId=>type(int),
        username=>type(str),
        password=>type(str),
        amount=>type(str),
        callback=>type(str),
        (optional)additionalData=>type(str),
    ]
    ```
	*
	* @param Gateway $gateway
	* @param array 	 $responseInfo
	*
	* @return mixed
	*/
	public static function request( array $gatewayInfo):mixed{
		return self::$driver->request($gatewayInfo);
	}


	/**
	*
	* Checking the transaction result
    *
    *******************************
    ###	To send the information of `Zarinpal` portal, send gateway Info like this
    ```php
    array[
         amount=>type(str),
        authority=>type(int),
        mmerchantId=>type(str),
    ]
    ```
    *******************************
    ###	To send the information of `Melat` portal, send gateway Info like this
    ```php
     array[
        terminalId=>type(str),
        username=>type(int),
        password=>type(str),
        saleOrderId=>type(str),
        saleReferenceId=>type(str),
    ]
    ```
    *******************************
	*
	* @param Gateway $gateway
	* @param array 	 $responseInfo
	*
	* @return array|\Exception
	*/
	public static function verify(Gateway $gateway, array $responseInfo):mixed{
		return self::$driver->verify($responseInfo);
	}
}
