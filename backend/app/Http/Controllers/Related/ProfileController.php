<?php

namespace App\Http\Controllers\Related;

use App\Enums\RefundMethodEnum;
use App\Http\Controllers\Controller;
use App\Models\Legalinfo;
use App\Models\Profile;
use App\Models\Province;
use App\Models\Work;
use App\Services\SMS\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class ProfileController extends Controller
{
    /**
     * Get user data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getProfile(){
        $this->authorize('viewAny',Profile::class);
        $user = auth()->user();
        $user = $user->where('id',$user->id)->with(['profile'=>fn($q)=>$q->with('work'),'legalInfo'])->first();
        $works = Work::get();
        $provinces = Province::get();
        return response(['status'=>'success','data'=>['user'=>$user,'works'=>$works,'provinces'=>$provinces]]);
    }

    /**
     * Store or update Legal information
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function legalInfo(Request $request){
        $request->validate([
            'province_id'=>['required','numeric','exists:provinces,id'],
            'city_id'=>['required','numeric','exists:cities,id'],
            'organization_name'=>['required','string','max:255','unique:legalinfos,id,organization_name'],
            'economic_code'=>['nullable','numeric','digits:12,12','unique:legalinfos,id,economic_code'],
            'national_id'=>['required','numeric','unique:legalinfos,id,national_id'],
            'registration_id'=>['required','numeric','unique:legalinfos,id,registration_id'],
            'phone'=>['required','numeric','unique:legalinfos,id,phone'],
        ]);

        $user = auth()->user();
        $legal = $user->legalInfo;
        $legal ? $this->authorize('update',$legal) : $this->authorize('create',Legalinfo::class);

        $legal = Legalinfo::create([
            'user_id'=>$user->id,
            'province_id'=>$request->province_id,
            'city_id'=>$request->city_id,
            'organization_name'=>$request->organization_name,
            'economic_code'=>$request->economic_code,
            'national_id'=>$request->national_id,
            'registration_id'=>$request->registration_id,
            'phone'=>$request->phone,
        ]);
        return $legal ? response(['status'=>'success'],200) : response(['status'=>'error'],500);
    }

    /**
     * Store or update name
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertName(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate(['name'=>['required','string','max:255']]);
        return $this->sendRequest($request, 'name');
    }


    /**
     * Store or update nationalId
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertNationalId(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate(['national_id'=>['required','string','max:255','unique:profiles,id,national_id']]);
        return $this->sendRequest($request, 'national_id');
    }

    /**
     * Store or update password
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertPassword(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');

        $request->validate([
            'prev_password'=>['required','string','max:255','min:8'],
            'password'=>['required','string','confirmed','max:255','min:8']
        ]);

        if(Hash::check($request->prev_password,$user->password)){
            $user = $user->update(['password'=>Hash::make($request->password)]);
            return $user ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
        }
        return response(['status'=>'error','msg'=>'رمز عبور قبلی اشتباه است'],500);
    }

    /**
     * Store or update birthday
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertBirthday(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate(['birth_day'=>['required','string','max:255']]);
        return $this->sendRequest($request, 'birth_day');
    }

    /**
     * Store or update mobile
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertMobile(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate([
            'mobile'=>['required','numeric','digits:11,11','unique:profiles,id,mobile'],
            'confirm_code'=>['required','numeric']
        ]);
        if(SMS::driver(config('app.sms_driver'))->codeIsValid($request->mobile,$request->confirm_code)){
            return $user->update(['mobile'=>$request->mobile]) ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
        }
        return response(['status'=>'error','msg'=>'کد تایید اشتباه است'],500);
    }


    /**
     * Store or update Email
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertEmail(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate(['email'=>['required','email','string','max:255','unique:profiles,id,email']]);
        return $this->sendRequest($request, 'email');
    }

    /**
     * Store or update Job
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function insertWork(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate(['work_id'=>['required','numeric','exists:works,id']]);
        return $this->sendRequest($request, 'work_id');
    }

    /**
     * Store or update refundMethod
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function refundMethod(Request $request){
        $user = auth()->user();
        $this->setAuthorize($user->profile, 'update');
        $request->validate([
            'refund_method'=>['required','string',new Enum(RefundMethodEnum::class)],
            'shaba'=>['required_if:refund_method,==,bank','nullable','numeric','digits:24,24','unique:profile,id,shaba'],
        ]);
        $profile = Profile::updateOrCreate(['user_id'=>auth()->Id()],[
            'refund_method'=>$request->refund_method,
            'shaba'=>$request->shaba,
        ]);
        return $profile ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * @param $profile
     * @param string $method
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function setAuthorize($profile, string $method){
        $profile ? $this->authorize($method,$profile) : $this->authorize('create',Profile::class);
    }

    /**
     * Update or create data for profile
     * @param Request $request
     * @param string $key
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    private function sendRequest(Request $request, string $key){
        $profile = Profile::updateOrCreate(['user_id'=>auth()->Id()],[$key=>$request[$key]]);
        return $profile ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }
}
