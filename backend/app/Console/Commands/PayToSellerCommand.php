<?php

namespace App\Console\Commands;

use App\Enums\PaidByEnum;
use App\Enums\ShippingEnum;
use App\Enums\YesOrNoEnum;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayToSellerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:pay-to-seller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the commission percentage of the product category and pay the remaining amount to the seller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('shipping_status',ShippingEnum::DELIVERED_CUSTOMER->value)
            ->where('pay_to_seller',YesOrNoEnum::NO->value)
            ->with(['orderDetails'=>fn($q)=>$q->with(['user','product.category'])])->get();

        foreach ($orders as $order){
            $order->orderDetails->map(function($item)use($order){
                $amount = $item->amount;
                $commission = $item->product->category->commission;
                $amount = getDiscount($amount, $commission,false);
                $item->user->wallet()->update(['amount'=>DB::raw("amount + {$amount}")]);
                insertTransaction($order, (int) $item->user_id, (int) $amount, Str::random(8),PaidByEnum::SYSTEM);
            });
        }

    }
}
