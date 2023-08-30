<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use App\Models\Product;
use Illuminate\Console\Command;

class AmazingProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:amazing-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable products whose amazing offer has expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "\n[+] start process.\n";

        $products = Product::where('amazing_offer_status',StatusEnum::ACTIVE->value)
            ->whereDate('updated_at','<',now()->subHours(24))->get();

        $products->map(fn($item)=>$item->update(['amazing_offer_status'=>null,'amazing_offer_percent'=>null]));

        echo "[+] Disable " . $products->count() . " amazing offer products.";

        echo "\n[*] Done.\n";
    }
}
