<x-cms::layout.admin :title="$title" :chain="$chain" :header="$header">
    <x-cms::tabs id="group_tab" :current="$tabs['group_tab']"
                 :tabs="[1 => 'cms::main.general_properties', 2 => 'cms-store::main.list_subgroup', 3 => 'cms-store::main.list_merchandise']"
                 :class="[1 => 'gray']" :conditions="[2 => !empty($object->id), 3 => !empty($object->id)]">
        <x-slot name="tab1">
            <x-cms-form id="group" :url="$url" :method="$method" :objectId="$object->id">
                <x-slot name="sheet1">
                    <x-cms::form.string name="sort" type="text" width="1" maxlength="8"
                                        :label="__('cms::main.attr_sort')" :value="old('sort', $object->sort)"
                                        :messages="$errors->get('sort')" />
                    <x-cms::form.string name="name" type="text" width="5" maxlength="255" :label="__('cms::main.attr_name')"
                                        :value="old('name', $object->name)"
                                        :messages="$errors->get('name')" required autofocus />
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
