<?php

namespace Netto\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Netto\Services\CartService;
use Symfony\Component\HttpFoundation\Response;

class Cart
{
    public function handle(Request $request, Closure $next): Response
    {
        CartService::get();
        return $next($request);
    }
}
