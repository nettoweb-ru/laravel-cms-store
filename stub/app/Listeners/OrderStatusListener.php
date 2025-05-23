<?php

namespace App\Listeners;

use Netto\Events\OrderStatusChanged;

class OrderStatusListener
{
    /**
     * @param OrderStatusChanged $event
     * @return void
     */
    public function handle(OrderStatusChanged $event): void
    {

    }
}
