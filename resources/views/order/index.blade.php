<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain">
    <x-cms::list
        id="order"
        :url="route('admin.store.order.list')"
        :columns="[
            'id' => __('main.attr_id'),
            'created_at' => __('main.attr_created_at'),
            'total' => __('main.attr_total'),
            'user.name' => __('main.attr_user'),
            'status.slug' => __('main.attr_status_id'),
            'currency.slug' => __('main.currency'),
            'volume' => __('main.attr_volume'),
            'weight' => __('main.attr_weight'),
            'delivery.name' => __('main.attr_delivery_id'),
            'delivery_cost' => __('main.attr_delivery_cost'),
        ]"
        :default="['id', 'created_at', 'total', 'user.name', 'status.name']"
        :defaultSort="['created_at' => 'desc']"
        :title="__('main.list_order')"
        :actions="[
            'create' => route('admin.store.order.create'),
            'delete' => route('admin.store.order.delete'),
        ]"
    />
    <x-cms::list
        id="status"
        :url="route('admin.store.status.list')"
        :columns="[
            'id' => __('main.attr_id'),
            'name' => __('main.attr_name'),
            'slug' => __('main.attr_slug'),
            'is_default' => __('main.attr_is_default'),
        ]"
        :default="['name']"
        :defaultSort="['name' => 'asc']"
        :title="__('main.list_status')"
        :actions="[
            'create' => route('admin.store.status.create'),
            'delete' => route('admin.store.status.delete'),
        ]"
    />
</x-cms::layout.admin>
