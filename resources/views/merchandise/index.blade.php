<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list :url="route('admin.group.list', [], false)" />
    <x-cms::list :url="route('admin.merchandise.list', [], false)" />
</x-cms::layout.admin>
