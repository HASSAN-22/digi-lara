<?php

namespace App\Jobs;

use App\Enums\SmsTypeEnum;
use App\Services\SMS\SMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $mobiles, private readonly string $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SMS::driver(config('app.sms_driver'))->sendMessage($this->mobiles, $this->message, SmsTypeEnum::MESSAGE);
    }
}
