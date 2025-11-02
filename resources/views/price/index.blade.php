<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain">
    <x-cms::list
        id="price"
        :url="route('admin.store.price.list')"
        :columns="[
            'id' => __('main.attr_id'),
            'sort' => __('main.attr_sort'),
            'name' => __('main.attr_name'),
            'slug' => __('main.attr_slug'),
            'is_default' => __('main.attr_is_default'),
        ]"
        :default="['sort', 'name']"
        :defaultSort="['sort' => 'asc']"
        :title="__('main.list_price')"
        :actions="[
            'create' => route('admin.store.price.create'),
            'delete' => route('admin.store.price.delete'),
            'downloadCsv' => route('admin.store.price.download-csv'),
        ]"
    />
</x-cms::layout.admin>
