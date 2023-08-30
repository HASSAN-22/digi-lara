<?php

namespace App\Console\Commands;

use App\Enums\ShippingEnum;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:cancel-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orders whose payment took more than an hour will be canceled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "\n[+] start process.\n";
        $successCount = 0;
        $errCount = 0;
        $orderIds = [];
        Order::where('shipping_status',ShippingEnum::PAYMENT_PENDING)
            ->where('created_at','<',now()->addHours(1))->with(['user'=>fn($q)=>$q->with('wallet')])->get()->map(function($order)use(&$successCount, &$errCount, &$orderIds){
                DB::beginTransaction();
                try{
                    $order->shipping_status = ShippingEnum::AUTO_CANCELLED;
                    $wallet = $order->user->wallet();
                    if($wallet){
                        $wallet->update(['amount'=>DB::raw("amount + {$order->reduced_wallet}")]);
                        $order->reduced_wallet = 0;
                    }
                    $order->save();
                    DB::commit();
                    $successCount++;
                }catch (\Exception $e){
                    $errCount++;
                    $orderIds[] = $order->id;
                    DB::rollBack();
                    Log::error("Schedule for command `cancel-order` for order id {$order->id} with message {$e->getMessage()}");
                }
            });

        $this->line("\n[*] Success {$successCount}.","options=bold;fg=green");
        $this->line("\n[-] Error {$errCount} for order (id)s: ".implode(',',$orderIds).".","options=bold;fg=red");
        echo "\n[*] Done.\n";
    }
}
