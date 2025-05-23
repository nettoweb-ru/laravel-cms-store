<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain" :header="$header">
    <x-cms-form id="merchandise" :url="$url" :method="$method" :objectId="$object->id"
                :sheets="[1 => 'main.general_properties_common', 2 => 'main.general_seo', 3 => 'main.list_price', 4 => 'main.list_section']"
                :conditions="[3 => !empty($object->costsData), 4 => !empty($reference['section'])]" :lang="true">
        <x-slot name="sheet1">
            <x-cms::form.string name="sort" width="1" maxlength="8"
                :label="__('main.attr_sort')"
                :value="old('sort', $object->getAttribute('sort'))"
                :messages="$errors->get('sort')"
            />
            <x-cms::form.string name="name" width="8" maxlength="255"
                :label="__('main.attr_name')"
                :value="old_multilingual('name', $object)"
                :messages="errors_multilingual('name', $errors)"
                :required="true"
                :autofocus="true"
                :multilang="true"
                :transliterate="$object->exists ? '' : 'slug'"
            />
            <x-cms::form.checkbox name="is_active" width="3"
                :label="__('main.attr_is_active')"
                :value="old('is_active', $object->getAttribute('is_active'))"
                :messages="$errors->get('is_active')"
                :options="$reference['boolean']"
            />
            <x-cms::form.string name="slug" width="6" maxlength="255"
                :label="__('main.attr_vendor_code')"
                :value="old('slug', $object->getAttribute('slug'))"
                :messages="$errors->get('slug')"
                :required="true"
            />
            <x-cms::form.datetime name="created_at" width="3"
                :label="__('main.attr_created_at')"
                :value="$object->getAttribute('created_at')"
                :disabled="true"
            />
            <x-cms::form.datetime name="updated_at" width="3"
                :label="__('main.attr_updated_at')"
                :value="$object->getAttribute('updated_at')"
                :disabled="true"
            />
            <x-cms::form.string name="weight" width="3" maxlength="8"
                :label="__('main.attr_weight')"
                :value="old('weight', $object->getAttribute('weight'))"
                :messages="$errors->get('weight')"
            />
            <x-cms::form.string name="length" width="3" maxlength="8"
                :label="__('main.attr_length')"
                :value="old('length', $object->getAttribute('length'))"
                :messages="$errors->get('length')"
            />
            <x-cms::form.string name="width" width="3" maxlength="8"
                :label="__('main.attr_width')"
                :value="old('width', $object->getAttribute('width'))"
                :messages="$errors->get('width')"
            />
            <x-cms::form.string name="height" width="3" maxlength="8"
                :label="__('main.attr_height')"
                :value="old('height', $object->getAttribute('height'))"
                :messages="$errors->get('height')"
            />
            <x-cms::form.file name="photo" width="6"
                :label="__('main.attr_photo')"
                :value="$object->getAttribute('photo')"
                :messages="errors_upload('photo', $errors)"
            />
            <x-cms::form.autocomplete name="album_id" width="6"
                :label="__('main.attr_album_id')"
                :value="old('album_id', $object->getAttribute('album_id'))"
                :messages="$errors->get('album_id')"
                :options="$reference['albums']"
            />
            <x-cms::form.editor name="description"
                :label="__('main.attr_description')"
                :value="old_multilingual('description', $object)"
                :messages="errors_multilingual('description', $errors)"
                :height="200"
                :multilang="true"
            />
        </x-slot>
        <x-slot name="sheet2">
            <x-cms::form.string name="meta_title" maxlength="255"
                :label="__('main.attr_meta_title')"
                :value="old_multilingual('meta_title', $object)"
                :messages="errors_multilingual('meta_title', $errors)"
                :multilang="true"
            />
            <x-cms::form.text name="meta_keywords" width="6" class="h120"
                :label="__('main.attr_meta_keywords')"
                :value="old_multilingual('meta_keywords', $object)"
                :messages="errors_multilingual('meta_keywords', $errors)"
                :multilang="true"
            />
            <x-cms::form.text name="meta_description" width="6" class="h120"
                :label="__('main.attr_meta_description')"
                :value="old_multilingual('meta_description', $object)"
                :messages="errors_multilingual('meta_description', $errors)"
                :multilang="true"
            />
            <x-cms::form.string name="og_title" maxlength="255"
                :label="__('main.attr_og_title')"
                :value="old_multilingual('og_title', $object)"
                :messages="errors_multilingual('og_title', $errors)"
                :multilang="true"
            />
            <x-cms::form.text name="og_description" class="h120"
                :label="__('main.attr_og_description')"
                :value="old_multilingual('og_description', $object)"
                :messages="errors_multilingual('og_description', $errors)"
                :multilang="true"
            />
        </x-slot>
        <x-slot name="sheet3">
            @foreach ($object->costsData as $priceId => $cost)
                <x-cms::form.string name="costs|{{ $priceId }}|value" width="6" maxlength="9"
                    :label="$cost['name']"
                    :value="old('costs|'.$priceId.'|value', $cost['value'])"
                    :messages="$errors->get('costs|'.$priceId.'|value')"
                    :required="true"
                />
                <x-cms::form.select name="costs|{{ $priceId }}|currency_id" width="6"
                    :label="__('main.currency')"
                    :value="old('costs|'.$priceId.'|currency_id', $cost['currency_id'])"
                    :messages="$errors->get('costs|'.$priceId.'|currency_id')"
                    :options="$reference['currency']"
                    :required="true"
                />
            @endforeach
        </x-slot>
        <x-slot name="sheet4">
            <x-cms::form.select name="sections" class="h250"
                :value="old('sections', $object->sections->pluck('id')->all())"
                :options="$reference['section']"
                :multiple="true"
            />
        </x-slot>
    </x-cms-form>
</x-cms::layout.admin>
