<?php

namespace Netto\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public ?string $sourceCode;
    public string $targetCode;

    /**
     * @param Order $order
     * @param string|null $sourceCode
     * @param string $targetCode
     */
    public function __construct(Order $order, ?string $sourceCode, string $targetCode)
    {
        $this->order = $order;
        $this->sourceCode = $sourceCode;
        $this->targetCode = $targetCode;
    }
}
