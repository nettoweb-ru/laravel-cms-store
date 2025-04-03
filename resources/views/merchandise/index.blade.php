<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list
        :url="route('admin.group.list', [], false)"
        id="store-group"
        :columns="[
            'id' => __('cms::main.attr_id'),
            'sort' => __('cms::main.attr_sort'),
            'name' => __('cms::main.attr_name'),
            'slug' => __('cms::main.attr_slug'),
            'created_at' => __('cms::main.attr_created_at'),
            'updated_at' => __('cms::main.attr_updated_at'),
        ]"
        :default="['name']"
    />
    <x-cms::list
        :url="route('admin.merchandise.list', [], false)"
        id="store-merchandise"
        :columns="[
            'id' => __('cms::main.attr_id'),
            'sort' => __('cms::main.attr_sort'),
            'name' => __('cms::main.attr_name'),
            'slug' => __('cms-store::main.attr_vendor_code'),
            'width' => __('cms-store::main.attr_width'),
            'length' => __('cms-store::main.attr_length'),
            'height' => __('cms-store::main.attr_height'),
            'weight' => __('cms-store::main.attr_weight'),
            'created_at' => __('cms::main.attr_created_at'),
            'updated_at' => __('cms::main.attr_updated_at'),
        ]"
        :default="['sort', 'name']"
    />
</x-cms::layout.admin>
