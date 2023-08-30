<?php

use App\Enums\SentStatusEnum;
use App\Enums\SmsTypeEnum;
use App\Jobs\SmsJob;
use App\Mail\EmailMail;
use App\Models\Email;
use App\Models\User;
use App\Services\SMS\SMS;
use App\Services\SMS\SmsService;
use Illuminate\Support\Facades\Mail;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/contact', function (\Illuminate\Http\Request $request) {
//    $mobiles = User::get()->pluck('mobile')->filter(fn($item)=>$item)->toArray();
//    dd(SMS::driver(config('app.sms_driver'))->sendMessage(['09168963472'], 'test', SmsTypeEnum::MESSAGE));

//    dispatch(new SmsJob($mobiles, 'test'));
});
