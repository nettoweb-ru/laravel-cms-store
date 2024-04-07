<?php

namespace Netto\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Netto\Models\Cart;
use Netto\Models\CartItem;
use Netto\Models\Delivery;
use App\Models\Merchandise;
use App\Models\Order;

abstract class CartService
{
    private const ID = 'cart';
    private static ?Cart $cart = null;

    /**
     * @param int $merchandiseId
     * @param int $quantity
     * @param string|null $priceCode
     * @return bool
     */
    public static function add(int $merchandiseId, int $quantity = 1, ?string $priceCode = null): bool
    {
        if ($quantity < 1) {
            return false;
        }

        /** @var Merchandise $merchandise */
        $merchandise = Merchandise::where('id', $merchandiseId)->where('is_active', '1')->get()->find($merchandiseId);
        if (empty($merchandise)) {
            return false;
        }

        if (is_null($priceCode)) {
            $priceCode = PriceService::getDefaultCode();
        }

        $prices = PriceService::getList();
        if (!array_key_exists($priceCode, $prices)) {
            return false;
        }

        $cost = null;
        foreach ($merchandise->costs->all() as $item) {
            if ($item->id == $prices[$priceCode]['id']) {
                $cost = $item->pivot;
                break;
            }
        }

        if (is_null($cost)) {
            return false;
        }

        $cart = self::get();
        $cartItem = null;

        foreach ($cart->items->all() as $item) {
            if ($item->merchandise_id == $merchandise->id) {
                $cartItem = $item;
                break;
            }
        }

        /** @var CartItem $cartItem */
        if (is_null($cartItem)) {
            $cartItem = new CartItem();
            $cartItem->setAttribute('cart_id', $cart->id);
            $cartItem->setAttribute('merchandise_id', $merchandise->id);
            $cartItem->setAttribute('quantity', $quantity);
            $cartItem->setAttribute('currency_id', $cost->currency_id);
            $cartItem->setAttribute('price', $cost->value);
        } else {
            if ($cartItem->currency_id != $cost->currency_id) {
                return false;
            }
            $cartItem->setAttribute('quantity', ($cartItem->quantity + $quantity));
        }

        if (method_exists($merchandise, 'getMultiLangAttributeValue')) {
            $name = $merchandise->getMultiLangAttributeValue('title', app()->getLocale());
        } else {
            $name = $merchandise->name;
        }
        $cartItem->setAttribute('name', $name);
        $cartItem->setAttribute('slug', $merchandise->slug);

        $cartItem->setAttribute('cost', ($cartItem->price * $cartItem->quantity));

        if (!$cartItem->save()) {
            return false;
        }

        $cart->setAttribute('expires_at', self::expires());
        if (!$cart->save()) {
            return false;
        }

        $cart->refresh();
        return true;
    }

    /**
     * @param array $fields
     * @param string|null $currencyCode
     * @return bool|Order
     */
    public static function checkout(array $fields = [], ?string $currencyCode = null): bool|Order
    {
        $cart = self::get();

        if (is_null($currencyCode)) {
            $currencyCode = CurrencyService::getDefaultCode();
        }

        $total = $cart->getTotal($currencyCode);
        if ($total == 0) {
            return false;
        }

        $currencies = CurrencyService::getList();
        $order = new Order();

        $order->status_id = OrderStatusService::getDefaultId();
        $order->total = $total;
        $order->currency_id = $currencies[$currencyCode]['id'];
        $order->volume = $cart->getVolume();
        $order->weight = $cart->getWeight();

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::getUser();
            $order->user_id = $user->id;
        }

        foreach ($fields as $key => $value) {
            $order->setAttribute($key, $value);
        }

        if (!$order->save()) {
            return false;
        }

        $cart->setAttribute('order_id', $order->id);
        $cart->setAttribute('expires_at', null);
        $cart->setAttribute('slug', null);

        if (!$cart->save()) {
            return false;
        }

        self::$cart = self::create();
        return $order;
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        $cart = self::get();
        foreach ($cart->items->all() as $item) {
            /** @var CartItem $item */
            $item->delete();
        }

        $cart->setAttribute('expires_at', self::expires());
        $cart->save();

        $cart->refresh();
    }

    /**
     * @return Cart
     */
    public static function get(): Cart
    {
        if (is_null(self::$cart)) {
            self::$cart = self::init();
        }

        return self::$cart;
    }

    /**
     * @param int $cartItemId
     * @param int|null $quantity
     * @return bool
     */
    public static function remove(int $cartItemId, ?int $quantity = null): bool
    {
        if (is_int($quantity) && ($quantity < 1)) {
            return false;
        }

        $cart = self::get();
        $cartItem = null;

        foreach ($cart->items->all() as $item) {
            if ($item->id == $cartItemId) {
                $cartItem = $item;
                break;
            }
        }

        /** @var CartItem $cartItem */
        if (is_null($cartItem)) {
            return false;
        }

        if (is_null($quantity)) {
            $quantity = $cartItem->quantity;
        }

        $quantityNew = $cartItem->quantity - $quantity;

        if ($quantityNew > 0) {
            $cartItem->setAttribute('quantity', $quantityNew);
            $cartItem->setAttribute('cost', ($cartItem->price * $quantityNew));

            if (!$cartItem->save()) {
                return false;
            }
        } else {
            $cartItem->delete();
        }

        $cart->setAttribute('expires_at', self::expires());
        if (!$cart->save()) {
            return false;
        }

        $cart->refresh();
        return true;
    }

    /**
     * @return Cart
     */
    private static function create(): Cart
    {
        $return = new Cart();
        $return->setAttribute('expires_at', self::expires());
        $return->save();

        Cookie::queue(Cookie::make(self::ID, $return->slug, (config('cms-store.cart_lifetime') * 1440)));
        return $return;
    }

    /**
     * @return string
     */
    private static function expires(): string
    {
        return date('Y-m-d H:i:s', time() + (config('cms-store.cart_lifetime') * 86400));
    }

    /**
     * @return Cart
     */
    private static function init(): Cart
    {
        $return = null;
        $id = Cookie::get(self::ID);

        if (!is_null($id)) {
            $get = Cart::where('slug', $id)->with('items')->get();
            if (count($get)) {
                $return = $get->get(0);
            }
        }

        if (is_null($return)) {
            $return = self::create();
        }

        return $return;
    }
}
