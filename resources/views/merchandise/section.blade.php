<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain" :header="$header">
    <x-cms::tabs id="section-tabs-{{ (int) $object->id }}"
                 :tabs="[1 => 'main.general_properties', 2 => 'main.list_subsection', 3 => 'main.list_merchandise']"
                 :conditions="[2 => $object->exists, 3 => $object->exists]">
        <x-slot name="tab1">
            <x-cms-form id="section" :url="$url" :method="$method" :objectId="$object->id" :lang="true"
                        :sheets="[1 => 'main.general_properties_common', 2 => 'main.general_seo']">
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
                        :label="__('main.attr_slug')"
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
                    <x-cms::form.select name="parent_id"
                        :label="__('main.attr_parent_id')"
                        :value="old('parent_id', $object->getAttribute('parent_id'))"
                        :messages="$errors->get('parent_id')"
                        :options="$reference['parent']"
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
            </x-cms-form>
        </x-slot>
        @if ($object->exists)
            <x-slot name="tab2">
                <x-cms::list
                    id="section-{{ (int) $object->id }}"
                    :url="route('admin.store.section.list', ['parent' => $object->id])"
                    :columns="[
                        'id' => __('main.attr_id'),
                        'sort' => __('main.attr_sort'),
                        'name' => __('main.attr_name'),
                        'slug' => __('main.attr_slug'),
                        'created_at' => __('main.attr_created_at'),
                        'updated_at' => __('main.attr_updated_at'),
                    ]"
                    :default="['name']"
                    :actions="[
                        'create' => route('admin.store.section.create', ['parent' => $object->id]),
                        'delete' => route('admin.store.section.delete'),
                        'toggle' => route('admin.store.section.toggle'),
                        'downloadCsv' => route('admin.store.section.download-csv', ['parent' => $object->id]),
                    ]"
                />
            </x-slot>
            <x-slot name="tab3">
                <x-cms::list
                    id="merchandise-{{ (int) $object->id }}"
                    :url="route('admin.store.merchandise.list', ['parent' => $object->id])"
                    :columns="[
                        'id' => __('main.attr_id'),
                        'sort' => __('main.attr_sort'),
                        'name' => __('main.attr_name'),
                        'slug' => __('main.attr_vendor_code'),
                        'width' => __('main.attr_width'),
                        'length' => __('main.attr_length'),
                        'height' => __('main.attr_height'),
                        'weight' => __('main.attr_weight'),
                        'created_at' => __('main.attr_created_at'),
                        'updated_at' => __('main.attr_updated_at'),
                    ]"
                    :default="['sort', 'name']"
                    :actions="[
                        'create' => route('admin.store.merchandise.create', ['parent' => $object->id]),
                        'delete' => route('admin.store.merchandise.delete'),
                        'toggle' => route('admin.store.merchandise.toggle'),
                        'downloadCsv' => route('admin.store.merchandise.download-csv', ['parent' => $object->id]),
                    ]"
                />
            </x-slot>
        @endif
    </x-cms::tabs>
</x-cms::layout.admin>
