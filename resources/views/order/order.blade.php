@php
    /** @var \App\Models\Order $object */
    $key = 0;
@endphp

<x-cms::layout.admin :head="$head" :url="$url" :chain="$chain" :header="$header">
    <x-cms::tabs id="order_tab"
                    :tabs="[1 => 'main.general_properties', 2 => 'main.order_history']"
                    :conditions="[2 => $object->exists]">
        <x-slot name="tab1">
            <x-cms-form id="order" :url="$url" :method="$method" :objectId="$object->id"
                        :sheets="[1 => 'main.general_properties_common', 2 => 'main.list_order_item']" >
                <x-slot name="sheet1">
                    <x-cms::form.autocomplete name="user_id" width="6"
                        :label="__('main.attr_user')"
                        :value="old('user_id', $object->getAttribute('user_id'))"
                        :messages="$errors->get('user_id')"
                        :options="$reference['user']"
                    />
                    <x-cms::form.datetime name="created_at" width="3"
                        :label="__('main.attr_created_at')"
                        :value="$object->getAttribute('created_at')"
                        :disabled="true"
                    />
                    <x-cms::form.select name="status_id" width="3"
                        :label="__('main.attr_status_id')"
                        :value="old('status_id', $object->getAttribute('status_id'))"
                        :messages="$errors->get('status_id')"
                        :options="$reference['status']"
                        :required="true"
                        :disabled="!$object->exists"
                    />
                    <x-cms::form.select name="delivery_id" width="6"
                        :label="__('main.attr_delivery_id')"
                        :value="old('delivery_id', $object->getAttribute('delivery_id'))"
                        :messages="$errors->get('delivery_id')"
                        :options="$reference['delivery']"
                        :required="true"
                    />
                    <x-cms::form.string name="delivery_cost" width="3" maxlength="9"
                        :label="__('main.attr_delivery_cost')"
                        :value="old('delivery_cost', $object->getAttribute('delivery_cost'))"
                        :messages="$errors->get('delivery_cost')"
                    />
                    <x-cms::form.select name="currency_id" width="3"
                        :label="__('main.currency')"
                        :value="old('currency_id', $object->getAttribute('currency_id'))"
                        :messages="$errors->get('currency_id')"
                        :options="$reference['currency']"
                        :required="true"
                    />
                    <x-cms::form.string name="weight" width="3"
                        :label="__('main.attr_weight')"
                        :value="format_number($object->getAttribute('weight'))"
                        :disabled="true"
                    />
                    <x-cms::form.string name="volume" width="3"
                        :label="__('main.attr_volume')"
                        :value="format_number($object->getAttribute('volume'))"
                        :disabled="true"
                    />
                    <x-cms::form.string name="total" width="6"
                        :label="__('main.attr_total')"
                        :value="format_currency($object->getAttribute('total'), $object->currency->getAttribute('slug'))"
                        :disabled="true"
                    />
                </x-slot>
                <x-slot name="sheet2">
                    @foreach ($object->cartData as $id => $item)
                        <x-cms::form.autocomplete name="cart|{{ $id }}|merchandise_id" width="6" maxlength="255"
                            :label="$key ? '' : __('main.attr_name')"
                            :value="old('cart|'.$id.'|merchandise_id', $item['merchandise_id'])"
                            :messages="$errors->get('cart|'.$id.'|merchandise_id')"
                            :options="$reference['merchandise']"
                        />
                        <x-cms::form.string name="cart|{{ $id }}|price" width="2" maxlength="9"
                            :label="$key ? '' : __('main.attr_price')"
                            :value="old('cart|'.$id.'|price', $item['price'])"
                            :messages="$errors->get('cart|'.$id.'|price')"
                        />
                        <x-cms::form.string name="cart|{{ $id }}|quantity" width="2" maxlength="8"
                            :label="$key ? '' : __('main.attr_quantity')"
                            :value="old('cart|'.$id.'|quantity', $item['quantity'])"
                            :messages="$errors->get('cart|'.$id.'|quantity')"
                        />
                        <x-cms::form.string name="cart|{{ $id }}|cost" width="2"
                            :label="$key ? '' : __('main.attr_cost')"
                            :value="format_currency($item['cost'], $object->currency->getAttribute('slug'))"
                            :disabled="true"
                        />
                        @php
                            $key++;
                        @endphp
                    @endforeach

                    @for ($a = 0; $a < 5; $a++, $key++)
                        <x-cms::form.autocomplete name="cart_new|{{ $a }}|merchandise_id" width="6" maxlength="255"
                            :label="$key ? '' : __('main.attr_name')"
                            :value="old('cart_new|'.$a.'|merchandise_id')"
                            :messages="$errors->get('cart_new|'.$a.'|merchandise_id')"
                            :options="$reference['merchandise']"
                        />
                        <x-cms::form.string name="cart_new|{{ $a }}|price" width="2" maxlength="9"
                            :label="$key ? '' : __('main.attr_price')"
                            :value="old('cart_new|'.$a.'|price')"
                            :messages="$errors->get('cart_new|'.$a.'|price')"
                        />
                        <x-cms::form.string name="cart_new|{{ $a }}|quantity" width="2" maxlength="8"
                            :label="$key ? '' : __('main.attr_quantity')"
                            :value="old('cart_new|'.$a.'|quantity')"
                            :messages="$errors->get('cart_new|'.$a.'|quantity')"
                        />
                        <x-cms::form.string name="cart_new|{{ $a }}|cost" width="2"
                            :label="$key ? '' : __('main.attr_cost')"
                            :disabled="true"
                        />
                    @endfor
                </x-slot>
            </x-cms-form>
        </x-slot>
        <x-slot name="tab2">
            <table class="info">
                <thead>
                <tr>
                    <th class="col-3"><span class="text-small">{{ __('main.attr_status_id') }}</span></th>
                    <th class="col-4 sort asc"><span class="text-small">{{ __('main.attr_created_at') }}</span></th>
                    <th class="col-5"><span class="text-small">{{ __('main.attr_user') }}</span></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($history as $item)
                    <tr>
                        <td><span class="text">{{ $item['name'] }}</span></td>
                        <td><span class="text">{{ $item['date'] }}</span></td>
                        <td><span class="text">{{ $item['user'] }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-slot>
    </x-cms::tabs>
</x-cms::layout.admin>
