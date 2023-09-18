<?php


namespace App\Services\Bank;

use Illuminate\Http\RedirectResponse;

interface Gateway{

	/**
	*
	* Send the transaction to the bank portal
	* @param array $gatewayInfo
    * @return string
    * @throws \Exception
	*/
    public static function request(array $gatewayInfo): string;

	/**
	*
	* Checking the transaction result
	* @param array $responseInfo
    * @return array
    * @throws \Exception
	*/
	public static function verify(array $responseInfo): array;
}
