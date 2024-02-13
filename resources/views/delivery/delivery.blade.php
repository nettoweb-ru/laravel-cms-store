<x-cms::layout.admin :title="$title" :chain="$chain" :header="$header">
    <x-cms-form :url="$url" :method="$method" :objectId="$object->id" :lang="true">
        <x-slot name="sheet1">
            <x-cms::form.string name="sort" type="text" width="1" maxlength="8"
                                :label="__('cms::main.attr_sort')" :value="old('sort', $object->sort)"
                                :messages="$errors->get('sort')" />
            <x-cms::form.string name="title" type="text" width="5" maxlength="255"
                                :label="__('cms::main.attr_name')"
                                :value="$object->getMultiLangOldValue('title')"
                                :messages="$object->getMultiLangInputErrors($errors, 'title')" required
                                multilang autofocus />
            <x-cms::form.string name="slug" type="text" width="3" maxlength="255" :label="__('cms::main.attr_slug')" :value="old('slug', $object->slug)" :messages="$errors->get('slug')" required />
            <x-cms::form.checkbox name="is_active" width="3" type="radio"
                                  :label="__('cms::main.attr_is_active')"
                                  :value="old('is_active', $object->is_active)"
                                  :messages="$errors->get('is_active')" :options="$reference['boolean']"/>
            <x-cms::form.string name="weight_min" type="text" width="3" maxlength="9"
                                :label="__('cms-store::main.attr_weight_min')"
                                :value="old('weight_min', $object->weight_min)"
                                :messages="$errors->get('weight_min')"/>
            <x-cms::form.string name="weight_max" type="text" width="3" maxlength="9"
                                :label="__('cms-store::main.attr_weight_max')"
                                :value="old('weight_max', $object->weight_max)"
                                :messages="$errors->get('weight_max')"/>
            <x-cms::form.string name="volume_min" type="text" width="3" maxlength="17"
                                :label="__('cms-store::main.attr_volume_min')"
                                :value="old('volume_min', $object->volume_min)"
                                :messages="$errors->get('volume_min')"/>
            <x-cms::form.string name="volume_max" type="text" width="3" maxlength="17"
                                :label="__('cms-store::main.attr_volume_max')"
                                :value="old('volume_max', $object->volume_max)"
                                :messages="$errors->get('volume_max')"/>
            <x-cms::form.string name="total_min" type="text" width="3" maxlength="9"
                                :label="__('cms-store::main.attr_total_min')"
                                :value="old('total_min', $object->total_min)"
                                :messages="$errors->get('total_min')"/>
            <x-cms::form.string name="total_max" type="text" width="3" maxlength="9"
                                :label="__('cms-store::main.attr_total_max')"
                                :value="old('total_max', $object->total_max)"
                                :messages="$errors->get('total_max')"/>
            <x-cms::form.string name="cost" type="text" width="3" maxlength="9"
                                :label="__('cms-store::main.attr_cost')"
                                :value="old('cost', $object->cost)"
                                :messages="$errors->get('cost')"/>
            <x-cms::form.select name="currency_id" width="3"
                                :options="$reference['currency']"
                                :label="__('cms-currency::main.currency')"
                                :value="old('currency_id', $object->currency_id)"
                                :messages="$errors->get('currency_id')" required/>
            <x-cms::form.editor name="description" height="200" :label="__('cms::main.attr_description')"
                                :value="$object->getMultiLangOldValue('description')"
                                :messages="$object->getMultiLangInputErrors($errors, 'description')" multilang/>
            @permission('manage-access')
            <x-cms::form.checkbox name="roles" :label="__('cms::main.list_role')" :value="old('roles', $object->roles->pluck('id')->all())" :options="$reference['role']" :messages="$errors->get('roles')" multiple />
            @endpermission
        </x-slot>
    </x-cms-form>
</x-cms::layout.admin>
