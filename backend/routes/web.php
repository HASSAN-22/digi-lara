<?php

use App\Services\Bank\Payment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/bank', function () {
    $redirectUrl = Payment::driver('Zibal')->request(setGateway(2000, 1, '09168963472', 'www'));
    return redirect()->to($redirectUrl)->send();
});
