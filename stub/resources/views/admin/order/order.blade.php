<x-cms::layout.admin :title="$title" :chain="$chain" :header="$header">
    <x-cms::tabs id="order_tab" :tabs="[1 => 'cms::main.general_properties', 2 => 'cms-store::main.list_order_item', 3 => 'cms-store::main.order_history']">
        <x-slot name="tab1">
            <x-cms-form :url="$url" :method="$method" :objectId="$object->id" :apply="!$object->is_locked" :save="!$object->is_locked">
                <x-slot name="sheet1">
                    <x-cms::form.select name="status_id" width="3" :options="$reference['status']" :label="__('cms-store::main.attr_status_id')" :value="old('status_id', $object->status_id)" required :disabled="$object->is_locked" :messages="$errors->get('status_id')" />
                    <x-cms::form.datetime name="created_at" width="3" :label="__('cms::main.attr_created_at')" :value="old('created_at', $object->created_at)" disabled/>
                    <x-cms::form.string name="weight" type="text" width="3" maxlength="8"
                                        :label="__('cms-store::main.attr_weight')" :value="old('weight', $object->weight)" disabled />
                    <x-cms::form.string name="volume" type="text" width="3" maxlength="17"
                                        :label="__('cms-store::main.attr_volume')" :value="old('volume', $object->volume)" disabled />
                </x-slot>
            </x-cms-form>
        </x-slot>
        <x-slot name="tab2">
            <table class="info">
                <thead>
                <tr>
                    <th class="col-6"><span class="text-small">{{ __('cms::main.attr_name') }}</span></th>
                    <th class="col-3"><span class="text-small">{{ __('cms-store::main.attr_vendor_code') }}</span></th>
                    <th class="col-1 anti-align"><span class="text-small">{{ __('cms-store::main.attr_price') }}</span></th>
                    <th class="col-1 anti-align"><span class="text-small">{{ __('cms-store::main.attr_quantity') }}</span></th>
                    <th class="col-1 anti-align"><span class="text-small">{{ __('cms-store::main.attr_cost') }}</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($object->items->all() as $item)
                    <tr>
                        <td><span class="text">{{ $item->name }}</span></td>
                        <td><span class="text">{{ $item->slug }}</span></td>
                        <td class="anti-align"><span class="text">{{ format_currency($item->price, $item->currency->slug) }}</span></td>
                        <td class="anti-align"><span class="text">{{ format_number($item->quantity) }}</span></td>
                        <td class="anti-align"><span class="text">{{ format_currency($item->cost, $item->currency->slug) }}</span></td>
                    </tr>
                @endforeach
                <tr>
                    <td><span class="text">{{ $object->delivery->name }}</span></td>
                    <td><span class="text">{{ $object->delivery->slug }}</span></td>
                    <td class="anti-align"><span class="text">{{ format_currency($object->delivery_cost, $object->currency->slug) }}</span></td>
                    <td class="anti-align"><span class="text">{{ format_number(1) }}</span></td>
                    <td class="anti-align"><span class="text">{{ format_currency($object->delivery_cost, $object->currency->slug) }}</span></td>
                </tr>
                <tr class="strong">
                    <td colspan="4"><span class="text">{{ __('cms-store::main.attr_total') }}</span></td>
                    <td class="anti-align"><span class="text">{{ format_currency($object->total, $object->currency->slug) }}</span></td>
                </tr>
                </tbody>
            </table>
        </x-slot>
        <x-slot name="tab3">
            <table class="info">
                <thead>
                <tr>
                    <th class="col-3"><span class="text-small">{{ __('cms-store::main.attr_status_id') }}</span></th>
                    <th class="col-4 sort asc"><span class="text-small">{{ __('cms::main.attr_created_at') }}</span></th>
                    <th class="col-5"><span class="text-small">{{ __('cms::main.attr_user') }}</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($object->history->all() as $item)
                    <tr>
                        <td><span class="text">{{ "[{$item->status_id}] ".$item->status->name }}</span></td>
                        <td><span class="text">{{ format_date($item->created_at) }}</span></td>
                        <td><span class="text">{{ $item->user ? "[{$item->user_id}] ".$item->user->name : '' }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-slot>
    </x-cms::tabs>
</x-cms::layout.admin>
