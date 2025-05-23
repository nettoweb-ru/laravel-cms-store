<?php

namespace Netto\Console\Commands;

use Netto\Console\Commands\Abstract\Command as BaseCommand;
use Netto\Services\CartService;

class DeleteExpiredCarts extends BaseCommand
{
    protected $signature = 'cms:delete-expired-carts';
    protected $description = 'Delete abandoned and expired anonymous user carts';

    /**
     * @return void
     */
    protected function action(): void
    {
        CartService::deleteExpired();
    }
}
