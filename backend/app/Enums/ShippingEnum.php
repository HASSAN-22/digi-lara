<?php

namespace App\Enums;

enum ShippingEnum: string
{
    case PAYMENT_PENDING = 'payment_pending';
    case REVIEW_QUEUE = 'review_queue';
    case ORDER_CONFIRMATION = 'order_confirmation';
    case ORDER_PREPARATION = 'order_preparation';
    case LEAVING_CENTER = 'leaving_center';
    case PUADC = 'pick_up_at_distribution_centers';
    case DELIVERY_SENDER = 'delivery_to_sender_agent';
    case DELIVERED_CUSTOMER = 'delivered_to_the_customer';
    case CANCELLED = 'cancelled';
    case AUTO_CANCELLED = 'auto_cancelled';
    case RETURNED = 'returned';
    case PENDING_DELIVERY_TO_STORE='pending_delivery_to_store';
    case PENDING_PAY_BACK='pending_pay_back';
    case SUCCESS_PAY_BACK='success_pay_back';

    /**
     * @param string $status
     * @return array|string[]
     */
    public static function getIcon(string $status){
        now()->diff()->days;
        return match ($status){
            self::PAYMENT_PENDING->value => ['icon'=>'far fa-circle-exclamation','color'=>'#f9a825','percent'=>12.5],
            self::REVIEW_QUEUE->value => ['icon'=>'far fa-user-magnifying-glass','color'=>'#ae07d1','percent'=>12.5*2],
            self::ORDER_CONFIRMATION->value => ['icon'=>'far fa-thumbs-up','color'=>'#00e59e','percent'=>12.5*3],
            self::ORDER_PREPARATION->value => ['icon'=>'far fa-mug-hot','color'=>'#07b5d1','percent'=>12.5*4],
            self::LEAVING_CENTER->value => ['icon'=>'far fa-truck-fast','color'=>'#d0cb28','percent'=>12.5*5],
            self::PUADC->value => ['icon'=>'far fa-truck-ramp-couch','color'=>'#d107bc','percent'=>12.5*6],
            self::DELIVERY_SENDER->value => ['icon'=>'far fa-user-police','color'=>'#1c8ce7','percent'=>12.5*7],
            self::DELIVERED_CUSTOMER->value => ['icon'=>'far fa-circle-check','color'=>'#0cd13c','percent'=>12.5*8],
            self::CANCELLED->value => ['icon'=>'far fa-cart-xmark','color'=>'#ff0101'],
            self::AUTO_CANCELLED->value => ['icon'=>'far fa-circle-xmark','color'=>'#7a7a7a'],
            self::RETURNED->value => ['icon'=>'far fa-arrow-turn-down-left','color'=>'#7a7a7a'],
            default => [],
        };
    }
}
