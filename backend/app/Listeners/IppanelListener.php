<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IppanelListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $client = new \IPPanel\Client(config('services.ippanel.api_key'));
        $messageId = $client->send(
            config('services.ippanel.phone'),
            $event->recipients,
            $event->msg,
            ''
        );
    }
}
