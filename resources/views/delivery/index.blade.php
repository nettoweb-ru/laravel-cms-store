<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain">
    <x-cms::list
        id="delivery"
        :url="route('admin.store.delivery.list')"
        :columns="[
            'id' => __('main.attr_id'),
            'sort' => __('main.attr_sort'),
            'name' => __('main.attr_name'),
            'cost' => __('main.attr_cost'),
            'slug' => __('main.attr_slug'),
            'currency.slug' => __('main.currency'),
            'total_min' => __('main.attr_total_min'),
            'total_max' => __('main.attr_total_max'),
            'weight_min' => __('main.attr_weight_min'),
            'weight_max' => __('main.attr_weight_max'),
            'volume_min' => __('main.attr_volume_min'),
            'volume_max' => __('main.attr_volume_max'),
        ]"
        :default="['sort', 'name', 'currency_id']"
        :defaultSort="['sort' => 'asc']"
        :title="__('main.list_delivery')"
        :actions="[
            'create' => route('admin.store.delivery.create'),
            'delete' => route('admin.store.delivery.delete'),
            'toggle' => route('admin.store.delivery.toggle'),
        ]"
    />
</x-cms::layout.admin>
