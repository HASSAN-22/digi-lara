<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserAccessEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SMS\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function loginOrRegister(Request $request){
        $request->validate(['password'=>['required','string','min:8','max:255']]);

        $user = User::where('mobile',$request->mobile)->first();
        if($user){
            $checkPassword = Hash::check($request->password, $user->password);
            Auth::loginUsingId($user->id);
            return $checkPassword ? response(['status'=>'success', 'data'=>$this->setData($user)], 200) : response(['status'=>'error','error'=>'invalid login'],500);
        }

//        if(!SmsService::codeIsValid($request->mobile, $request->code)){
        if(!SMS::driver(config('app.sms_driver'))->codeIsValid($request->mobile, $request->code)){
            return response(['status'=>'error', 'error'=>'invalid code'],500);
        }

        $user = User::create([
            'name'=>$request->mobile,
            'mobile'=>$request->mobile,
            'access'=>UserAccessEnum::USER,
            'password'=>Hash::make($request->passowrd),
            'status'=>StatusEnum::ACTIVE,
            'avatar'=> '/avatar.jpg'
        ]);
        Auth::loginUsingId($user->id);
        return response(['status'=>'success','data'=>$this->setData($user)], 201);

    }

    public function logout(){
        $user = auth()->user();
        $user->tokens()->delete();
        return response(['status'=>'success']);
    }

    private function setData(User $user){
        $token = $user->generateToken();
        return ['token'=>$token,'access'=>$user->access,'name'=>$user->name,'id'=>$user->id,'avatar'=>$user->avatar,'status'=>$user->status];
    }
}
