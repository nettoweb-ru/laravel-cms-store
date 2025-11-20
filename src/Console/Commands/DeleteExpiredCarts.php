<?php

namespace Netto\Console\Commands;

use Illuminate\Console\Command;
use Netto\Services\CartService;

class DeleteExpiredCarts extends Command
{
    protected $signature = 'cms:delete-expired-carts';
    protected $description = 'Delete abandoned and expired anonymous user carts';

    /**
     * @return void
     */
    public function handle(): void
    {
        CartService::deleteExpired();
    }
}
