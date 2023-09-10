<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use App\Models\Product;
use Illuminate\Console\Command;

class ProductAmazingOfferResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:reset-amazing-offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A demo for resetting the expiration time of amazing products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expire = now()->addHours(24)->timestamp;
        Product::where('amazing_offer_status',StatusEnum::ACTIVE)->update(['amazing_offer_expire'=>$expire]);
    }
}
