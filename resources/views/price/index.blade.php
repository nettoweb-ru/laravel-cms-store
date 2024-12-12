<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list :url="route('admin.price.list', [], false)" id="store-price" />
</x-cms::layout.admin>
