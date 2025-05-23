<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain" :header="$header">
    <x-cms-form id="delivery" :url="$url" :method="$method" :objectId="$object->id" :lang="true">
        <x-slot name="sheet1">
            <x-cms::form.string name="sort" width="1" maxlength="3"
                :label="__('main.attr_sort')"
                :value="old('sort', $object->getAttribute('sort'))"
                :messages="$errors->get('sort')"
            />
            <x-cms::form.string name="name" width="5" maxlength="255"
                :label="__('main.attr_name')"
                :value="old_multilingual('name', $object)"
                :messages="errors_multilingual('name', $errors)"
                :required="true"
                :multilang="true"
                :autofocus="true"
                :transliterate="$object->exists ? '' : 'slug'"
            />
            <x-cms::form.string name="slug" width="3" maxlength="255"
                :label="__('main.attr_slug')"
                :value="old('slug', $object->getAttribute('slug'))"
                :messages="$errors->get('slug')"
                :required="true"
            />
            <x-cms::form.checkbox name="is_active" width="3"
                :label="__('main.attr_is_active')"
                :value="old('is_active', $object->getAttribute('is_active'))"
                :messages="$errors->get('is_active')"
                :options="$reference['boolean']"
            />
            <x-cms::form.string name="weight_min" width="3" maxlength="9"
                :label="__('main.attr_weight_min')"
                :value="old('weight_min', $object->getAttribute('weight_min'))"
                :messages="$errors->get('weight_min')"
            />
            <x-cms::form.string name="weight_max" width="3" maxlength="9"
                :label="__('main.attr_weight_max')"
                :value="old('weight_max', $object->getAttribute('weight_max'))"
                :messages="$errors->get('weight_max')"
            />
            <x-cms::form.string name="volume_min" width="3" maxlength="17"
                :label="__('main.attr_volume_min')"
                :value="old('volume_min', $object->getAttribute('volume_min'))"
                :messages="$errors->get('volume_min')"
            />
            <x-cms::form.string name="volume_max" width="3" maxlength="17"
                :label="__('main.attr_volume_max')"
                :value="old('volume_max', $object->getAttribute('volume_max'))"
                :messages="$errors->get('volume_max')"
            />
            <x-cms::form.string name="total_min" width="3" maxlength="9"
                :label="__('main.attr_total_min')"
                :value="old('total_min', $object->getAttribute('total_min'))"
                :messages="$errors->get('total_min')"
            />
            <x-cms::form.string name="total_max" width="3" maxlength="9"
                :label="__('main.attr_total_max')"
                :value="old('total_max', $object->getAttribute('total_max'))"
                :messages="$errors->get('total_max')"
            />
            <x-cms::form.string name="cost" width="3" maxlength="9"
                :label="__('main.attr_cost')"
                :value="old('cost', $object->getAttribute('cost'))"
                :messages="$errors->get('cost')"
            />
            <x-cms::form.select name="currency_id" width="3"
                :label="__('main.currency')"
                :value="old('currency_id', $object->getAttribute('currency_id'))"
                :messages="$errors->get('currency_id')"
                :options="$reference['currency']"
                :required="true"
            />
            <x-cms::form.editor name="description"
                :label="__('main.attr_description')"
                :value="old_multilingual('description', $object)"
                :messages="errors_multilingual('description', $errors)"
                :height="200"
                :multilang="true"
            />
            @permission('admin-access')
                <x-cms::form.checkbox name="permissions"
                    :label="__('main.list_permission')"
                    :value="old('permissions', $object->permissions->pluck('id')->all())"
                    :options="$reference['permission']"
                    :multiple="true"
                />
            @endpermission
        </x-slot>
    </x-cms-form>
</x-cms::layout.admin>
