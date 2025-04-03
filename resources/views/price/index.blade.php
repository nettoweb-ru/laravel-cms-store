<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list
        :url="route('admin.price.list', [], false)"
        id="store-price"
        :columns="[
            'id' => __('cms::main.attr_id'),
            'name' => __('cms::main.attr_name'),
            'slug' => __('cms::main.attr_slug'),
            'is_default' => __('cms::main.attr_is_default'),
        ]"
        :default="['name']"
    />
</x-cms::layout.admin>
