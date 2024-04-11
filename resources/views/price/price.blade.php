<x-cms::layout.admin :title="$title" :chain="$chain" :header="$header">
    <x-cms-form :url="$url" :method="$method" :objectId="$object->id">
        <x-slot name="sheet1">
            <x-cms::form.string name="name" type="text" width="6" maxlength="255" transliterate="{{ $object->exists ? '' : 'slug' }}" :label="__('cms::main.attr_name')" :value="old('name', $object->name)" :messages="$errors->get('name')" required autofocus />
            <x-cms::form.string name="slug" type="text" width="3" maxlength="255" :label="__('cms::main.attr_slug')" :value="old('slug', $object->slug)" :messages="$errors->get('slug')" required />
            <x-cms::form.checkbox name="is_default" width="3" type="radio" :label="__('cms::main.attr_is_default')" :value="old('is_default', $object->is_default)" :messages="$errors->get('is_default')" :options="$reference['boolean']" />
            @permission('manage-access')
            <x-cms::form.checkbox name="roles" :label="__('cms::main.list_role')" :value="old('roles', $object->roles->pluck('id')->all())" :options="$reference['role']" :messages="$errors->get('roles')" multiple />
            @endpermission
        </x-slot>
    </x-cms-form>
</x-cms::layout.admin>
