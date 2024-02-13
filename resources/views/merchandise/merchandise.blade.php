<x-cms::layout.admin :title="$title" :chain="$chain" :header="$header">
    <x-cms-form id="merchandise" :url="$url" :method="$method" :objectId="$object->id"
                :sheets="[1 => 'cms::main.general_properties_common', 2 => 'cms::main.general_seo', 3 => 'cms-store::main.list_price', 4 => 'cms-store::main.list_group']"
                :sheet="$sheets['merchandise_sheet']" :lang="true" :conditions="[3 => !empty($costs), 4 => !empty($reference['group'])]">
        <x-slot name="sheet1">
            <x-cms::form.string name="sort" type="text" width="1" maxlength="8"
                                :label="__('cms::main.attr_sort')" :value="old('sort', $object->sort)"
                                :messages="$errors->get('sort')" />
            <x-cms::form.string name="title" type="text" width="11" maxlength="255" :label="__('cms::main.attr_name')"
                                :value="$object->getMultiLangOldValue('title')"
                                :messages="$object->getMultiLangInputErrors($errors, 'title')" required multilang autofocus />
            <x-cms::form.datetime name="created_at" width="3" :label="__('cms::main.attr_created_at')"
                                  :value="old('created_at', $object->created_at)" disabled/>
            <x-cms::form.datetime name="updated_at" width="3" :label="__('cms::main.attr_updated_at')"
                                  :value="old('updated_at', $object->updated_at)" disabled/>
            <x-cms::form.string name="slug" type="text" width="3" maxlength="255" :label="__('cms-store::main.attr_vendor_code')"
                                :value="old('slug', $object->slug)" :messages="$errors->get('slug')" required/>
            <x-cms::form.checkbox name="is_active" width="3" type="radio" :label="__('cms::main.attr_is_active')"
                                  :value="old('is_active', $object->is_active)" :messages="$errors->get('is_active')"
                                  :options="$reference['boolean']"/>
            <x-cms::form.string name="weight" type="text" width="3" maxlength="8"
                                :label="__('cms-store::main.attr_weight')" :value="old('weight', $object->weight)"
                                :messages="$errors->get('weight')" />
            <x-cms::form.string name="length" type="text" width="3" maxlength="8"
                                :label="__('cms-store::main.attr_length')" :value="old('length', $object->length)"
                                :messages="$errors->get('length')" />
            <x-cms::form.string name="width" type="text" width="3" maxlength="8"
                                :label="__('cms-store::main.attr_width')" :value="old('width', $object->width)"
                                :messages="$errors->get('width')" />
            <x-cms::form.string name="height" type="text" width="3" maxlength="8"
                                :label="__('cms-store::main.attr_height')" :value="old('height', $object->height)"
                                :messages="$errors->get('height')" />
            <x-cms::form.editor name="content" height="200" :label="__('cms::main.attr_description')"
                                :value="$object->getMultiLangOldValue('content')"
                                :messages="$object->getMultiLangInputErrors($errors, 'content')" multilang/>
        </x-slot>
        <x-slot name="sheet2">
            <x-cms::form.string name="meta_title" type="text" maxlength="255" :label="__('cms::main.attr_meta_title')"
                                :value="$object->getMultiLangOldValue('meta_title')"
                                :messages="$object->getMultiLangInputErrors($errors, 'meta_title')" multilang/>
            <x-cms::form.text name="meta_keywords" width="6" class="h120" :label="__('cms::main.attr_meta_keywords')"
                              :value="$object->getMultiLangOldValue('meta_keywords')"
                              :messages="$object->getMultiLangInputErrors($errors, 'meta_keywords')" multilang/>
            <x-cms::form.text name="meta_description" width="6" class="h120"
                              :label="__('cms::main.attr_meta_description')"
                              :value="$object->getMultiLangOldValue('meta_description')"
                              :messages="$object->getMultiLangInputErrors($errors, 'meta_description')" multilang/>
            <x-cms::form.string name="og_title" type="text" maxlength="255" :label="__('cms::main.attr_og_title')"
                                :value="$object->getMultiLangOldValue('og_title')"
                                :messages="$object->getMultiLangInputErrors($errors, 'og_title')" multilang/>
            <x-cms::form.text name="og_description" class="h120" :label="__('cms::main.attr_og_description')"
                              :value="$object->getMultiLangOldValue('og_description')"
                              :messages="$object->getMultiLangInputErrors($errors, 'og_description')" multilang/>
        </x-slot>
        <x-slot name="sheet3">
            @foreach ($costs as $priceId => $cost)
                <x-cms::form.string name="costs|{{ $priceId }}|value" type="text" width="6" maxlength="9"
                                    :label="$cost['name']" :value="old('costs|'.$priceId.'|value', $cost['value'])"
                                    :messages="$errors->get('costs|'.$priceId.'|value')" required/>
                <x-cms::form.select name="costs|{{ $priceId }}|currency_id" width="6"
                                    :options="$reference['currency']"
                                    :label="__('cms-currency::main.currency')"
                                    :value="old('costs|'.$priceId.'|currency_id', $cost['currency_id'])"
                                    :messages="$errors->get('costs|'.$priceId.'|currency_id')" required/>
            @endforeach
        </x-slot>
        <x-slot name="sheet4">
            <x-cms::form.select class="h250" name="groups"
                                :options="$reference['group']"
                                :value="old('groups', $object->groups->pluck('id')->all())"
                                :messages="$errors->get('groups')" multiple/>
        </x-slot>
    </x-cms-form>
</x-cms::layout.admin>
