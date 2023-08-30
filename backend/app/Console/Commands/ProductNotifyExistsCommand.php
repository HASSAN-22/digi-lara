<?php

namespace App\Console\Commands;

use App\Enums\SmsTypeEnum;
use App\Mail\ProductNotifyExistsMail;
use App\Models\Productnotifyexists;
use App\Services\SMS\SMS;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProductNotifyExistsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:notify-exists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Users will be notified if the product is available';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "\n[+] start process.\n";

        $results = Productnotifyexists::whereRelation('product','count','>','0')->with([
            'product',
            'user'=>fn($q)=>$q->select('id','name')->with('profile')
        ])->get();
        $shopName = config('app.name');
        foreach ($results as $item){
            $message = "کاربر {$item->user->name} کالای {$item->product->ir_name} موجود شده است میتوانید برای خرید به {$shopName} مراجعه نمایید";
            // Send sms
            $this->sendSms($item, $message);

            // Send mail
            $profile = $item->user->profile;
            if($profile and $profile->email){
                Mail::send(new ProductNotifyExistsMail($profile->email, $item->product, $message));
            }
        }

        echo "\n[*] Done.\n";
    }

    /**
     * @param mixed $item
     * @param string $message
     * @return void
     */
    private function sendSms(mixed $item, string $message): void
    {
        if(SMS::driver(config('app.sms_driver'))->sendMessage([$item->user->mobile], $message, SmsTypeEnum::MESSAGE)){
            $this->line("[*] success send sms for " . $item->user->mobile,"options=bold;fg=green");
        }else {
            $this->line("[-] Error send sms for " . $item->user->mobile,"options=bold;fg=red");
        }
    }
}
