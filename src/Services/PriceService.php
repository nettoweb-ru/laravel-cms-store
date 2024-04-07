<?php

namespace Netto\Services;

use Netto\Models\Price;

abstract class PriceService
{
    /**
     * @return int
     */
    public static function getDefaultCode(): string
    {
        static $return;

        if (is_null($return)) {
            foreach (self::getList() as $code => $item) {
                if ($item['is_default']) {
                    $return = $code;
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * @return int
     */
    public static function getDefaultId(): int
    {
        static $return;

        if (is_null($return)) {
            foreach (self::getList() as $item) {
                if ($item['is_default']) {
                    $return = $item['id'];
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        static $return;

        if (is_null($return)) {
            $return = [];
            foreach (Price::with('roles')->get() as $item) {
                /** @var Price $item */
                $return[$item->slug] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'is_default' => $item->is_default,
                    'roles_id' => $item->roles->pluck('id')->all(),
                ];
            }
        }

        return $return;
    }
}
