<?php

use Netto\Models\Cart;
use Netto\Models\Currency;
use Netto\Services\{CartService, SectionService, OrderService};

if (!function_exists('find_section_node')) {
    /**
     * Return section node by ID.
     *
     * @param int $id
     * @return array|null
     */
    function find_section_node(int $id): ?array
    {
        return SectionService::findNode($id);
    }
}

if (!function_exists('get_available_deliveries')) {
    /**
     * Return the list of available deliveries for current user cart.
     *
     * @param Cart $cart
     * @return array
     */
    function get_available_deliveries(Cart $cart): array
    {
        return CartService::getDeliveries($cart);
    }
}

if (!function_exists('get_default_order_status_id')) {
    /**
     * Return ID of default order status.
     *
     * @return int
     */
    function get_default_order_status_id(): int
    {
        return OrderService::getDefaultStatusId();
    }
}

if (!function_exists('get_default_price_code')) {
    /**
     * Return slug of default price.
     *
     * @return string
     */
    function get_default_price_code(): string
    {
        return OrderService::getDefaultPriceCode();
    }
}

if (!function_exists('get_delivery_list')) {
    /**
     * @return array
     */
    function get_delivery_list(): array
    {
        return OrderService::getDeliveries();
    }
}

if (!function_exists('get_labels_order_status')) {
    /**
     * Returns associative array [$id => $slug + $name] for order status list.
     *
     * @return array
     */
    function get_labels_order_status(): array
    {
        static $return;
        if (is_null($return)) {
            $return = [];
            foreach (get_order_status_list() as $slug => $status) {
                $return[$status['id']] = "[{$slug}] {$status['name']}";
            }
        }

        return $return;
    }
}

if (!function_exists('get_labels_sections')) {
    /**
     * Returns associative array [$id => indented $name] for section list.
     *
     * @param int|null $skipId
     * @param bool $emptyLead
     * @return array
     */
    function get_labels_sections(?int $skipId, bool $emptyLead = false): array
    {
        return SectionService::getLabels($skipId, $emptyLead);
    }
}

if (!function_exists('get_order_status_list')) {
    /**
     * Return the list of order statuses.
     *
     * @return array
     */
    function get_order_status_list(): array
    {
        return OrderService::getStatusList();
    }
}

if (!function_exists('get_price_list')) {
    /**
     * Return the list of prices.
     *
     * @param bool $public
     * @return array
     */
    function get_price_list(bool $public = false): array
    {
        return OrderService::getPriceList($public);
    }
}

if (!function_exists('get_rules_costs')) {
    /**
     * Returns the list of rules for cost values.
     *
     * @return array
     */
    function get_rules_costs(): array
    {
        $return = [];
        foreach (get_price_list() as $code => $item) {
            $return["costs|{$code}|value"] = ['decimal:0,2', 'gt:0', 'max:999999.99'];
            $return["costs|{$code}|currency_id"] = ['required', 'integer', 'exists:'.Currency::class.',id'];
        }

        return $return;
    }
}
