<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list :url="route('admin.order.list', [], false)" id="store-order" />
    <x-cms::list :url="route('admin.status.list', [], false)" id="store-status" />
</x-cms::layout.admin>
