<?php

namespace Netto\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Auth, Cookie};
use Illuminate\Support\Str;

use App\Models\{Merchandise, Order, User};
use Netto\Exceptions\NettoException;
use Netto\Models\{Cart, CartItem, Cost, Delivery, Price};

abstract class CartService
{
    private static Cart $cart;
    private const COOKIE_ID = 'netto-cart';

    /**
     * @param string|null $cartId
     * @param string|int $itemId
     * @param string $priceCode
     * @param string|int $quantity
     * @return bool
     * @throws NettoException
     */
    public static function add(?string $cartId, string|int $itemId, string $priceCode = 'retail', string|int $quantity = 1): bool
    {
        $cart = self::load($cartId);

        $quantity = (int) $quantity;
        if ($quantity < 1) {
            return false;
        }

        $merchandise = Merchandise::query()->where('id', $itemId)->where('is_active', '1')->with('costs')->first();
        if (is_null($merchandise)) {
            return false;
        }

        $priceList = get_price_list(true);
        if (!array_key_exists($priceCode, $priceList)) {
            return false;
        }

        $cost = null;
        foreach ($merchandise->costs->all() as $item) {
            /** @var Price $item */
            if ($item->getAttribute('id') == $priceList[$priceCode]['id']) {
                $cost = $item->pivot;
                break;
            }
        }

        if (is_null($cost)) {
            return false;
        }

        $cartItem = null;
        foreach ($cart->items->all() as $item) {
            if (($item->getAttribute('merchandise_id') == $itemId) && ($item->getAttribute('currency_id') == $cost->getAttribute('currency_id'))) {
                $cartItem = $item;
                break;
            }
        }

        /** @var Cost $cost */
        if (is_null($cartItem)) {
            $cartItem = new CartItem();

            $cartItem->setAttribute('cart_id', $cart->getAttribute('id'));
            $cartItem->setAttribute('merchandise_id', $itemId);
            $cartItem->setAttribute('currency_id', $cost->getAttribute('currency_id'));
            $cartItem->setAttribute('price', $cost->getAttribute('value'));
            $cartItem->setAttribute('quantity', $quantity);
        } else {
            $cartItem->setAttribute('quantity', $cartItem->getAttribute('quantity') + $quantity);
        }

        if (!$cartItem->save()) {
            return false;
        }

        return self::update($cart);
    }

    /**
     * @param string|null $cartId
     * @param array $attributes
     * @return Order|null
     * @throws NettoException
     */
    public static function checkout(?string $cartId, array $attributes = []): ?Order
    {
        $cart = self::load($cartId);

        if (!self::update($cart) || !$cart->items->count()) {
            return null;
        }

        $deliveries = get_available_deliveries($cart);
        if (!array_key_exists($attributes['delivery_id'], $deliveries)) {
            return null;
        }

        $currencyCode = find_currency_code($cart->getAttribute('currency_id'));

        foreach ($cart->items->all() as $item) {
            /** @var CartItem $item */
            if ($item->getAttribute('currency_id') == $cart->getAttribute('currency_id')) {
                continue;
            }

            $item->setAttribute('price', convert_currency(
                $item->getAttribute('price'),
                find_currency_code($item->getAttribute('currency_id')),
                $currencyCode
            ));
            $item->setAttribute('currency_id', $cart->getAttribute('currency_id'));

            if (!$item->save()) {
                return null;
            }
        }

        $delivery = $deliveries[$attributes['delivery_id']];

        $return = new Order();
        $return->setAttribute('currency_id', $cart->getAttribute('currency_id'));
        $return->setAttribute('status_id', get_default_order_status_id());

        if ($delivery['currency_id'] == $cart->getAttribute('currency_id')) {
            $deliveryCost = $delivery['cost'];
        } else {
            $deliveryCost = convert_currency(
                $delivery['cost'],
                find_currency_code($delivery['currency_id']),
                $currencyCode
            );
        }

        $return->setAttribute('delivery_cost', $deliveryCost);

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::getUser();
            $return->setAttribute('user_id', $user->getAttribute('id'));
        }

        foreach ($attributes as $attribute => $value) {
            $return->setAttribute($attribute, $value);
        }

        $orderCart = new Cart();
        $orderCart->setAttribute('currency_id', $cart->getAttribute('currency_id'));
        $orderCart->setRelation('items', $cart->items);

        $return->setRelation('cart', $orderCart);
        if (!$return->save()) {
            return null;
        }

        $orderCart->setAttribute('order_id', $return->getAttribute('id'));

        if (!$orderCart->save()) {
            return null;
        }

        foreach ($orderCart->items->all() as $item) {
            /** @var CartItem $item */
            $item->setAttribute('cart_id', $orderCart->getAttribute('id'));
            $item->save();
        }

        return $return;
    }

    /**
     * @param string|null $cartId
     * @return bool
     * @throws NettoException
     */
    public static function clear(?string $cartId): bool
    {
        $cart = self::load($cartId);

        foreach ($cart->items->all() as $item) {
            /** @var CartItem $item */
            if (!$item->delete()) {
                return false;
            }
        }

        return self::update($cart);
    }

    /**
     * Delete anonymous abandoned carts.
     *
     * @return void
     */
    public static function deleteExpired(): void
    {
        Cart::query()->whereNull('order_id')->whereNotNull('expires_at')->where('expires_at', '<', date('Y-m-d H:i:s'))->delete();
    }

    /**
     * Return current cart. For AJAX calls, use load() instead.
     *
     * @return Cart
     */
    public static function get(): Cart
    {
        return self::$cart;
    }

    /**
     * Return the list of available deliveries for user cart.
     *
     * @param Cart $cart
     * @return array
     */
    public static function getDeliveries(Cart $cart): array
    {
        $builder = Delivery::query()->orderBy('sort')->with(['translated', 'permissions'])->where('is_active', '1');

        if ($total = $cart->getTotal()) {
            $builder->where(function(Builder $builder) use ($total) {
                $builder->where('total_min', '<=', $total);
                $builder->orWhereNull('total_min');
            });

            $builder->orWhere(function(Builder $builder) use ($total) {
                $builder->where('total_max', '>', $total);
                $builder->orWhereNull('total_max');
            });
        }

        if ($volume = $cart->getVolume()) {
            $builder->where(function(Builder $builder) use ($volume) {
                $builder->where('volume_min', '<=', $volume);
                $builder->orWhereNull('volume_min');
            });

            $builder->orWhere(function(Builder $builder) use ($volume) {
                $builder->where('volume_max', '>', $volume);
                $builder->orWhereNull('volume_max');
            });
        }

        if ($weight = $cart->getWeight()) {
            $builder->where(function(Builder $builder) use ($weight) {
                $builder->where('weight_min', '<=', $weight);
                $builder->orWhereNull('weight_min');
            });

            $builder->orWhere(function(Builder $builder) use ($weight) {
                $builder->where('weight_max', '>', $weight);
                $builder->orWhereNull('weight_max');
            });
        }

        $return = [];
        foreach ($builder->get() as $item) {
            /** @var Delivery $item */
            if ($item->isAccessible()) {
                $return[$item->getAttribute('id')] = [
                    'id' => $item->getAttribute('id'),
                    'slug' => $item->getAttribute('slug'),
                    'cost' => $item->getAttribute('cost'),
                    'currency_id' => $item->getAttribute('currency_id'),
                    'currency_code' => find_currency_code($item->getAttribute('currency_id')),
                    'name' => $item->name,
                    'description' => $item->description,
                ];
            }
        }

        return $return;
    }

    /**
     * @return void
     */
    public static function init(): void
    {
        if ($cookie = Cookie::get(self::COOKIE_ID)) {
            $builder = Cart::query()
                ->select(['slug', 'currency_id', 'id'])
                ->with('items')
                ->where('slug', $cookie)
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', date('Y-m-d H:i:s'));

            if ($builder->count()) {
                self::setConfigValues($builder->first());
                return;
            } else {
                Cookie::forget(self::COOKIE_ID);
            }
        }

        $cart = new Cart();
        $cart->setAttribute('expires_at', self::getExpirationDate());
        $cart->setAttribute('currency_id', get_default_currency_id());

        do {
            $slug = Str::random(64);
            if (Cart::query()->select('id')->where('slug', $slug)->count() == 0) {
                break;
            }
        } while (true);

        $cart->setAttribute('slug', $slug);
        $cart->save();

        self::setConfigValues($cart);

        Cookie::queue(Cookie::make(self::COOKIE_ID, $slug, self::getLifetime() * 1440));
    }

    /**
     * Load cart by ID.
     *
     * @param string|null $id
     * @return Cart
     * @throws NettoException
     */
    public static function load(?string $id): Cart
    {
        if (is_null($id)) {
            throw new NettoException('Cart ID cannot be empty');
        }

        $builder = Cart::query()
            ->with('items')
            ->where('slug', $id)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', date('Y-m-d H:i:s'));

        if ($builder->count() == 0) {
            throw new NettoException('Cart was not found');
        }

        return $builder->first();
    }

    /**
     * @param string|null $cartId
     * @param string|int $cartItemId
     * @param string|int|null $quantity
     * @return bool
     * @throws NettoException
     */
    public static function remove(?string $cartId, string|int $cartItemId, null|string|int $quantity = null): bool
    {
        $cart = self::load($cartId);

        $cartItem = $cart->items->find((int) $cartItemId);
        if (is_null($cartItem)) {
            return false;
        }

        if (is_null($quantity)) {
            $quantity = $cartItem->getAttribute('quantity');
        } else {
            $quantity = (int) $quantity;
        }

        if ($quantity < 1) {
            return false;
        }

        $after = $cartItem->getAttribute('quantity') - $quantity;

        if ($after > 0) {
            $cartItem->setAttribute('quantity', $after);
            if (!$cartItem->save()) {
                return false;
            }
        } else if (!$cartItem->delete()) {
            return false;
        }

        return self::update($cart);
    }

    /**
     * @param string|null $cartId
     * @param string $currencyCode
     * @return bool
     * @throws NettoException
     */
    public static function setCurrency(?string $cartId, string $currencyCode): bool
    {
        $cart = self::load($cartId);

        $currency = get_currency_list();
        if (!array_key_exists($currencyCode, $currency)) {
            return false;
        }

        $cart->setAttribute('currency_id', $currency[$currencyCode]['id']);

        return self::update($cart);
    }

    /**
     * @return string
     */
    private static function getExpirationDate(): string
    {
        return date('Y-m-d H:i:s', time() + (self::getLifetime() * 86400));
    }

    /**
     * @return int
     */
    private static function getLifetime(): int
    {
        return config('cms-store.cart_lifetime', 30);
    }

    /**
     * @return void
     */
    private static function setCartId(): void
    {
        config()->set('cart_id', Cookie::get(self::COOKIE_ID));
    }

    /**
     * @param Cart $cart
     * @return void
     */
    private static function setConfigValues(Cart $cart): void
    {
        config()->set('cart_id', $cart->getAttribute('slug'));
        config()->set('cart_currency', find_currency_code($cart->getAttribute('currency_id')));

        self::$cart = $cart;
    }

    /**
     * @param Cart $cart
     * @return bool
     */
    private static function update(Cart $cart): bool
    {
        $cart->setAttribute('expires_at', self::getExpirationDate());
        if (!$cart->save()) {
            return false;
        }

        $cart->refresh();
        return true;
    }
}
