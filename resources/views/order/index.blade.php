<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list :url="route('admin.order.list', [], false)" />
    <x-cms::list :url="route('admin.status.list', [], false)" />
</x-cms::layout.admin>
