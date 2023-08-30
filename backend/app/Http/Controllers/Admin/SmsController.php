<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SmsTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SmsRequest;
use App\Jobs\SmsJob;
use App\Models\Sms;
use App\Models\User;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Sms::class);
        $sms = Sms::where('type',SmsTypeEnum::MESSAGE)->latest()->search()->paginate(10);
        $mobiles = User::get()->pluck('mobile')->filter(fn($item)=>$item)->toArray();
        return response(['status'=>'success','data'=>['sms'=>$sms,'mobiles'=>$mobiles]]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SmsRequest $request)
    {
        $this->authorize('create',Sms::class);
        dispatch(new SmsJob($request->mobiles, $request->message));
        return response(['status'=>'success'],201);
    }

}
