<?php

namespace Netto\Services;

use Netto\Models\OrderStatus;

abstract class OrderStatusService
{
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
            foreach (OrderStatus::all() as $item) {
                $return[$item->slug] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'is_default' => $item->is_default,
                    'is_final' => $item->is_final,
                ];
            }
        }

        return $return;
    }
}
