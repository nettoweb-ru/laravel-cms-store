<?php

namespace Netto\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Netto\Models\Delivery;

abstract class DeliveryService
{
    /**
     * @return array
     */
    public static function getList(): array
    {
        $return = [];

        $cart = CartService::get();
        $builder = Delivery::orderBy('sort')->with(['translated', 'currency', 'roles'])->where('is_active', '1');

        if ($total = $cart->getTotal()) {
            $builder->where(function(Builder $builder) use ($total) {
                $builder->where('total_min', '<=', $total);
            });
            $builder->where(function(Builder $builder) use ($total) {
                $builder->where('total_max', '>', $total);
                $builder->orWhere('total_max', 0);
            });
        }

        if ($volume = $cart->getVolume()) {
            $builder->where(function(Builder $builder) use ($volume) {
                $builder->where('volume_min', '<=', $volume);
            });
            $builder->where(function(Builder $builder) use ($volume) {
                $builder->where('volume_max', '>', $volume);
                $builder->orWhere('volume_max', 0);
            });
        }

        if ($weight = $cart->getWeight()) {
            $builder->where(function(Builder $builder) use ($weight) {
                $builder->where('weight_min', '<=', $weight);
            });
            $builder->where(function(Builder $builder) use ($weight) {
                $builder->where('weight_max', '>', $weight);
                $builder->orWhere('weight_max', 0);
            });
        }

        $userRoleId = [];
        if ($user = Auth::user()) {
            /** @var User $user */
            $userRoleId = $user->roles->pluck('id')->all();
        }

        $lang = app()->getLocale();

        foreach ($builder->get() as $item) {
            /** @var Delivery $item */
            if ($userRoleId && ($deliveryRoleId = $item->roles->pluck('id')->all()) && !array_intersect($deliveryRoleId, $userRoleId)) {
                continue;
            }

            $return[$item->id] = [
                'id' => $item->id,
                'name' => $item->getMultiLangAttributeValue('title', $lang),
                'cost' => $item->cost,
                'currency' => $item->currency->slug,
                'description' => $item->getMultiLangAttributeValue('description', $lang),
            ];
        }

        return $return;
    }
}
