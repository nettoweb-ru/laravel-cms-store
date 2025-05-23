<?php

namespace Netto\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Netto\Services\CartService;
use Symfony\Component\HttpFoundation\Response;

class Cart
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isXmlHttpRequest()) {
            CartService::init();
        }

        return $next($request);
    }
}
