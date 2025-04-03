<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list
        :url="route('admin.delivery.list', [], false)"
        id="store-delivery"
        :columns="[
            'id' => __('cms::main.attr_id'),
            'sort' => __('cms::main.attr_sort'),
            'name' => __('cms::main.attr_name'),
            'cost' => __('cms-store::main.attr_cost'),
            'slug' => __('cms::main.attr_slug'),
            'currency_id' => __('cms-currency::main.currency'),
            'total_min' => __('cms-store::main.attr_total_min'),
            'total_max' => __('cms-store::main.attr_total_max'),
            'weight_min' => __('cms-store::main.attr_weight_min'),
            'weight_max' => __('cms-store::main.attr_weight_max'),
            'volume_min' => __('cms-store::main.attr_volume_min'),
            'volume_max' => __('cms-store::main.attr_volume_max'),
        ]"
        :default="['sort', 'name', 'currency_id']"
    />
</x-cms::layout.admin>
