<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SmsTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Mail\ContactUsMail;
use App\Models\Contact;
use App\Services\SMS\SMS;
use App\Services\SMS\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Contact::class);
        $contacts = Contact::latest()->search()->paginate(10);
        return ContactResource::collection($contacts);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $this->authorize('view',$contact);
        return response(['status'=>'success','data'=>$contact]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate(['answer'=>['required','string','max:1000']]);
        $this->authorize('update',$contact);
        $smsCheck = false;
        $emailCheck = false;
        try {
            SMS::driver(config('app.sms_driver'))->sendMessage([$contact->mobile], $request->answer, SmsTypeEnum::MESSAGE);
            $smsCheck = true;
        }catch (\Exception $e){}
        try {
            Mail::send(new ContactUsMail($request->answer, $contact->email));
            $emailCheck = true;
        }catch (\Exception $e){}

        if($smsCheck or $emailCheck){
            $contact->update(['answer'=>$request->answer]);
            return response(['status'=>'success'],201);
        }
        return response(['status'=>'error'],500);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
