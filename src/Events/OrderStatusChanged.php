<?php

namespace Netto\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public string $statusCode;

    /**
     * @param Order $order
     * @param string $statusCode
     */
    public function __construct(Order $order, string $statusCode)
    {
        $this->order = $order;
        $this->statusCode = $statusCode;
    }
}
