<?php

namespace App\Listeners;

use Netto\Events\OrderStatusChanged;

class OrderStatusChangeLogic
{
    public function handle(OrderStatusChanged $event): void
    {

    }
}
