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
                $request->input('cartId'),
                $request->input('itemId'),
                $request->input('priceCode'),
                $request->input('quantity')
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
        $order = CartService::checkout($request->input('cartId'), $request->validated());
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
                $request->input('cartId')
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
                $request->input('cartId'),
                $request->input('currencyCode')
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
                $request->input('cartId'),
                $request->input('cartItemId'),
                $request->input('quantity')
            )
        );
    }
}
