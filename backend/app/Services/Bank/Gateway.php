<?php


namespace App\Services\Bank;

use Illuminate\Http\RedirectResponse;

interface Gateway{

	/**
	*
	* Send the transaction to the bank portal
	* @param array $gatewayInfo
    * @return RedirectResponse|void
    * @throws \Exception
	*/
    public static function request(array $gatewayInfo);

	/**
	*
	* Checking the transaction result
	* @param array $responseInfo
    * @return RedirectResponse|void
    * @throws \Exception
	*/
	public static function verify(array $responseInfo);
}
