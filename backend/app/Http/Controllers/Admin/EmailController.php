<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Resources\EmailResource;
use App\Jobs\EmailJob;
use App\Mail\EmailMail;
use App\Models\Email;
use App\Models\Newsletter;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Email::class);
        $emails = Email::search()->latest()->with(['user'=>fn($q)=>$q->select('id','name')])->paginate(10);
        return EmailResource::collection($emails);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmailRequest $request)
    {
        $this->authorize('create',Email::class);
        dispatch(new EmailJob(auth()->Id(),$request->emails, $request->message, $request->issue));
        return response(['status'=>'success'],201);
    }

    /**
     * Re send email
     * @param Email $email
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function reSend(Email $email){
        $this->authorize('create',Email::class);
        try {
            if(Mail::send(new EmailMail(email:$email->emails, message:$email->message, issue:$email->issue)) instanceof \Illuminate\Mail\SentMessage) {
                Email::where('email',$email->emails)->update(['status'=>SentStatusEnum::SENT]);
                return response(['status'=>'success'],201);
            }else{
                Email::where('email',$email->emails)->update(['status'=>SentStatusEnum::NOT_SENT]);
                return response(['status'=>'error'],500);
            }
        }catch (\Exception $e){
            Email::where('email',$email->emails)->update(['status'=>SentStatusEnum::NOT_SENT]);
            return response(['status'=>'error'],500);
        }
    }

    /**
     * Get emails from newsletter or profiles
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getEmails(Request $request){
        $this->authorize('viewAny',Email::class);
        if($request->type == 'newsletter'){
            $emails = Newsletter::get()->pluck('email');
        }else{
            $emails = Profile::get()->pluck('email');
        }
        return response(['status'=>'success','data'=>$emails->filter(fn($item)=>$item)->toArray()]);
    }
}
