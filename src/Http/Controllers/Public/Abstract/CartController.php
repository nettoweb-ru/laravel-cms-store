<?php

namespace Netto\Http\Controllers\Public\Abstract;

use App\Http\Controllers\Public\Abstract\Controller as BaseController;
use App\Http\Requests\Public\CheckoutRequest;
use Illuminate\Http\{JsonResponse, Request};
use Netto\Exceptions\NettoException;
use Netto\Services\CartService;

abstract class CartController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NettoException
     */
    public function add(Request $request): JsonResponse
    {
        return response()->json(
            CartService::add(
                $request->get('cartId'),
                $request->get('itemId'),
                $request->get('priceCode'),
                $request->get('quantity')
            )
        );
    }

    /**
     * @param CheckoutRequest $request
     * @return JsonResponse
     * @throws NettoException
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $order = CartService::checkout($request->get('cartId'), $request->validated());
        return response()->json(!is_null($order));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NettoException
     */
    public function clear(Request $request): JsonResponse
    {
        return response()->json(
            CartService::clear(
                $request->get('cartId')
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NettoException
     */
    public function currency(Request $request): JsonResponse
    {
        return response()->json(
            CartService::setCurrency(
                $request->get('cartId'),
                $request->get('currencyCode')
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NettoException
     */
    public function remove(Request $request): JsonResponse
    {
        return response()->json(
            CartService::remove(
                $request->get('cartId'),
                $request->get('cartItemId'),
                $request->get('quantity')
            )
        );
    }
}
