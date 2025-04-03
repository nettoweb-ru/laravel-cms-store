<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list
        :url="route('admin.order.list', [], false)"
        id="store-order"
        :columns="[
            'id' => __('cms::main.attr_id'),
            'created_at' => __('cms::main.attr_created_at'),
            'total' => __('cms-store::main.attr_total'),
            'user_id' => __('cms::main.attr_user'),
            'status_id' => __('cms-store::main.attr_status_id'),
            'currency_id' => __('cms-currency::main.currency'),
            'is_locked' => __('cms-store::main.attr_is_locked'),
            'volume' => __('cms-store::main.attr_volume'),
            'weight' => __('cms-store::main.attr_weight'),
        ]"
        :default="['id', 'created_at', 'total', 'user_id', 'status_id']"
    />
    <x-cms::list
        :url="route('admin.status.list', [], false)"
        id="store-status"
        :columns="[
            'id' => __('cms::main.attr_id'),
            'name' => __('cms::main.attr_name'),
            'slug' => __('cms::main.attr_slug'),
            'is_default' => __('cms::main.attr_is_default'),
            'is_final' => __('cms-store::main.attr_is_final'),
        ]"
        :default="['name']"
    />
</x-cms::layout.admin>
