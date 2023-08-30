<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SmsTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SMS\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function sendCode(Request $request){
        $request->validate(['mobile'=>['required','numeric','digits:11,11','exists:users,mobile']]);
//        SMS::driver(config('app.sms_driver'))->sendMessage([$request->mobile], \App\Services\SMS\SmsService::generateCode(), SmsTypeEnum::CODE);
        return response(['status'=>'success'],200);
    }


    public function forgotPassword(Request $request){
        $request->validate([
            'password'=>['required','string','min:8','max:255'],
            'code'=>['required','numeric','digits:6,6']
        ]);

        if(!SMS::driver(config('app.sms_driver'))->codeIsValid($request->mobile, $request->code)){
            return response(['status'=>'error', 'error'=>'invalid code'],500);
        }

        $user = User::where('mobile',$request->mobile)->update(['password'=>Hash::make($request->password)]);
        Auth::logout();
        return $user ? response(['status'=>'success'], 200) : response(['status'=>'error'],500);
    }
}
