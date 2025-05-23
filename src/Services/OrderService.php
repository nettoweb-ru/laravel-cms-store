<?php

namespace Netto\Services;

use Netto\Models\{Delivery, OrderStatus, Price};

abstract class OrderService
{
    /**
     * Return ID of default order status.
     *
     * @return int
     */
    public static function getDefaultStatusId(): int
    {
        static $return;
        if (is_null($return)) {
            foreach (self::getStatusList() as $item) {
                if ($item['is_default']) {
                    $return = $item['id'];
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * Return slug of default price.
     *
     * @return string
     */
    public static function getDefaultPriceCode(): string
    {
        static $return;
        if (is_null($return)) {
            foreach (self::getPriceList() as $code => $language) {
                if ($language['is_default']) {
                    $return = $code;
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public static function getDeliveries(): array
    {
        static $return;
        if (is_null($return)) {
            $return = [];
            foreach (Delivery::query()->where('is_active', '1')->get() as $item) {
                /** @var Delivery $item */
                $return[$item->getAttribute('slug')] = $item->toArray();
            }
        }

        return $return;
    }

    /**
     * Return the list of order statuses.
     *
     * @return array
     */
    public static function getStatusList(): array
    {
        static $return;
        if (is_null($return)) {
            $return = [];
            foreach (OrderStatus::with('translated')->get() as $item) {
                /** @var OrderStatus $item */
                $return[$item->getAttribute('slug')] = [
                    'id' => $item->getAttribute('id'),
                    'name' => $item->getTranslated('name')[get_default_language_code()],
                    'is_default' => $item->getAttribute('is_default'),
                ];
            }
        }

        return $return;
    }

    /**
     * Return the list of prices.
     *
     * @param bool $public
     * @return array
     */
    public static function getPriceList(bool $public = false): array
    {
        $key = (int) $public;

        static $return = [
            1 => null,
            0 => null,
        ];

        if (is_null($return[$key])) {
            $return[$key] = [];

            $builder = Price::with('translated');
            if ($public) {
                $builder->with('permissions');
                $language = app()->getLocale();
            } else {
                $language = get_default_language_code();
            }

            foreach ($builder->get() as $item) {
                /** @var Price $item */
                if ($public && !$item->isAccessible()) {
                    continue;
                }

                $return[$key][$item->getAttribute('slug')] = [
                    'id' => $item->getAttribute('id'),
                    'name' => $item->getTranslated('name')[$language],
                    'is_default' => $item->getAttribute('is_default'),
                ];
            }
        }

        return $return[$key];
    }
}
