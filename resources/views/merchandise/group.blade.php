<x-cms::layout.admin :title="$title" :chain="$chain" :header="$header">
    <x-cms::tabs id="group_tab" :current="$tabs['group_tab']"
                 :tabs="[1 => 'cms::main.general_properties', 2 => 'cms-store::main.list_subgroup', 3 => 'cms-store::main.list_merchandise']"
                 :class="[1 => 'gray']" :conditions="[2 => !empty($object->id), 3 => !empty($object->id)]">
        <x-slot name="tab1">
            <x-cms-form id="group" :url="$url" :method="$method" :objectId="$object->id"
                        :sheets="[1 => 'cms::main.general_properties_common', 2 => 'cms::main.general_seo']" :lang="true"
                        :sheet="$sheets['group_sheet']">
                <x-slot name="sheet1">
                    <x-cms::form.string name="sort" type="text" width="1" maxlength="8"
                                        :label="__('cms::main.attr_sort')" :value="old('sort', $object->sort)"
                                        :messages="$errors->get('sort')" />
                    <x-cms::form.string name="title" type="text" width="5" maxlength="255"
                                        :label="__('cms::main.attr_name')"
                                        :value="$object->getMultiLangOldValue('title')"
                                        :messages="$object->getMultiLangInputErrors($errors, 'title')" required
                                        multilang autofocus />
                    <x-cms::form.string name="slug" type="text" width="3" maxlength="255"
                                        :label="__('cms::main.attr_slug')" :value="old('slug', $object->slug)"
                                        :messages="$errors->get('slug')" required/>
                    <x-cms::form.checkbox name="is_active" width="3" type="radio"
                                          :label="__('cms::main.attr_is_active')"
                                          :value="old('is_active', $object->is_active)"
                                          :messages="$errors->get('is_active')" :options="$reference['boolean']"/>
                    <x-cms::form.select name="parent_id" width="6"
                                        :options="\Netto\Services\GroupService::getLabels($object->id)"
                                        :label="__('cms-store::main.attr_parent_id')"
                                        :value="old('parent_id', $object->parent_id)"
                                        :messages="$errors->get('parent_id')"/>
                    <x-cms::form.datetime name="created_at" width="3" :label="__('cms::main.attr_created_at')"
                                          :value="old('created_at', $object->created_at)" disabled/>
                    <x-cms::form.datetime name="updated_at" width="3" :label="__('cms::main.attr_updated_at')"
                                          :value="old('updated_at', $object->updated_at)" disabled/>
                    <x-cms::form.editor name="content" height="200" :label="__('cms::main.attr_description')"
                                        :value="$object->getMultiLangOldValue('content')"
                                        :messages="$object->getMultiLangInputErrors($errors, 'content')" multilang/>
                </x-slot>
                <x-slot name="sheet2">
                    <x-cms::form.string name="meta_title" type="text" maxlength="255"
                                        :label="__('cms::main.attr_meta_title')"
                                        :value="$object->getMultiLangOldValue('meta_title')"
                                        :messages="$object->getMultiLangInputErrors($errors, 'meta_title')" multilang/>
                    <x-cms::form.text name="meta_keywords" width="6" class="h120"
                                      :label="__('cms::main.attr_meta_keywords')"
                                      :value="$object->getMultiLangOldValue('meta_keywords')"
                                      :messages="$object->getMultiLangInputErrors($errors, 'meta_keywords')" multilang/>
                    <x-cms::form.text name="meta_description" width="6" class="h120"
                                      :label="__('cms::main.attr_meta_description')"
                                      :value="$object->getMultiLangOldValue('meta_description')"
                                      :messages="$object->getMultiLangInputErrors($errors, 'meta_description')"
                                      multilang/>
                    <x-cms::form.string name="og_title" type="text" maxlength="255"
                                        :label="__('cms::main.attr_og_title')"
                                        :value="$object->getMultiLangOldValue('og_title')"
                                        :messages="$object->getMultiLangInputErrors($errors, 'og_title')" multilang/>
                    <x-cms::form.text name="og_description" class="h120" :label="__('cms::main.attr_og_description')"
                                      :value="$object->getMultiLangOldValue('og_description')"
                                      :messages="$object->getMultiLangInputErrors($errors, 'og_description')"
                                      multilang/>
                </x-slot>
            </x-cms-form>
        </x-slot>
        @if ($object->id)
            <x-slot name="tab2">
                <x-cms::list :url="route('admin.group.list', ['parent' => $object->id], false)"/>
            </x-slot>
            <x-slot name="tab3">
                <x-cms::list :url="route('admin.merchandise.list', ['parent' => $object->id], false)"/>
            </x-slot>
        @endif
    </x-cms::tabs>
</x-cms::layout.admin>
