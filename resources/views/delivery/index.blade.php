<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list :url="route('admin.delivery.list', [], false)" id="store-delivery" />
</x-cms::layout.admin>
