<?php

namespace App\Jobs;

use App\Enums\SentStatusEnum;
use App\Mail\EmailMail;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly int $userId,private readonly array $emails, private readonly string $message, private readonly string $issue)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        array_map(function($email){
            try{
                if(Mail::send(new EmailMail(email:$email, message:$this->message, issue:$this->issue)) instanceof \Illuminate\Mail\SentMessage) {
                    Email::create(['user_id'=>$this->userId,'email'=>$email,'issue'=>$this->issue,'message'=>$this->message,'status'=>SentStatusEnum::SENT]);
                }else{
                    Email::create(['user_id'=>$this->userId,'email'=>$email,'issue'=>$this->issue,'message'=>$this->message,'status'=>SentStatusEnum::NOT_SENT]);
                }
            }catch (\Exception $e){
                Email::create(['user_id'=>$this->userId,'email'=>$email,'issue'=>$this->issue,'message'=>$this->message,'status'=>SentStatusEnum::NOT_SENT]);
            }
        },$this->emails);
    }
}
