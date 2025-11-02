<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain">
    <x-cms::list
        id="section"
        :url="route('admin.store.section.list')"
        :columns="[
            'id' => __('main.attr_id'),
            'sort' => __('main.attr_sort'),
            'name' => __('main.attr_name'),
            'slug' => __('main.attr_slug'),
            'created_at' => __('main.attr_created_at'),
            'updated_at' => __('main.attr_updated_at'),
        ]"
        :default="['sort', 'name']"
        :defaultSort="['sort' => 'asc']"
        :title="__('main.list_section')"
        :actions="[
            'create' => route('admin.store.section.create'),
            'delete' => route('admin.store.section.delete'),
            'toggle' => route('admin.store.section.toggle'),
            'downloadCsv' => route('admin.store.section.download-csv'),
        ]"
    />
    <x-cms::list
        id="merchandise"
        :url="route('admin.store.merchandise.list')"
        :columns="[
            'id' => __('main.attr_id'),
            'sort' => __('main.attr_sort'),
            'name' => __('main.attr_name'),
            'slug' => __('main.attr_vendor_code'),
            'width' => __('main.attr_width'),
            'length' => __('main.attr_length'),
            'height' => __('main.attr_height'),
            'weight' => __('main.attr_weight'),
            'created_at' => __('main.attr_created_at'),
            'updated_at' => __('main.attr_updated_at'),
        ]"
        :default="['sort', 'name']"
        :defaultSort="['sort' => 'asc']"
        :title="__('main.list_merchandise')"
        :actions="[
            'create' => route('admin.store.merchandise.create'),
            'delete' => route('admin.store.merchandise.delete'),
            'toggle' => route('admin.store.merchandise.toggle'),
            'downloadCsv' => route('admin.store.merchandise.download-csv'),
        ]"
    />
</x-cms::layout.admin>
