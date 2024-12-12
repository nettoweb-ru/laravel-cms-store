<x-cms::layout.admin :title="$title" :chain="$chain">
    <x-cms::list :url="route('admin.group.list', [], false)" id="store-group"/>
    <x-cms::list :url="route('admin.merchandise.list', [], false)" id="store-merchandise" />
</x-cms::layout.admin>
