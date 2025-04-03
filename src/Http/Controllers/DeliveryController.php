<?php

namespace Netto\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Netto\Http\Requests\DeliveryRequest as WorkRequest;
use Netto\Models\Currency;
use Netto\Models\Delivery as WorkModel;
use Netto\Models\Role;
use Netto\Traits\CrudControllerActions;

class DeliveryController extends Abstract\AdminCrudController
{
    use CrudControllerActions;

    protected bool $autoSort = true;
    protected string $class = WorkModel::class;
    protected string $id = 'delivery';

    protected array $list = [
        'relations' => ['currency'],
        'title' => 'cms-store::main.list_delivery',
        'url' => [
            'create',
            'delete',
            'toggle',
        ],
    ];

    protected array $messages = [
        'create' => 'cms-store::main.create_delivery',
    ];

    protected array $route = [
        'index' => 'admin.delivery.index',
        'create' => 'admin.delivery.create',
        'delete' => 'admin.delivery.delete',
        'destroy' => 'admin.delivery.destroy',
        'edit' => 'admin.delivery.edit',
        'store' => 'admin.delivery.store',
        'toggle' => 'admin.delivery.toggle',
        'update' => 'admin.delivery.update',
    ];

    protected array $sync = [
        'roles',
    ];

    protected string $title = 'cms-store::main.list_delivery';

    protected array $view = [
        'index' => 'cms-store::delivery.index',
        'edit' => 'cms-store::delivery.delivery'
    ];

    /**
     * @param WorkRequest $request
     * @return RedirectResponse
     */
    public function store(WorkRequest $request): RedirectResponse
    {
        return $this->_store($request);
    }

    /**
     * @param WorkRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(WorkRequest $request, string $id): RedirectResponse
    {
        return $this->_update($request, $id);
    }

    /**
     * @param WorkModel $object
     * @return array
     */
    protected function getItem($object): array
    {
        $return = parent::getItem($object);
        if ($object->currency) {
            foreach (['cost', 'total_min', 'total_max'] as $item) {
                if (isset($return[$item])) {
                    $return[$item] = format_currency($return[$item], $object->currency->slug);
                }
            }

            if (isset($return['currency_id'])) {
                $return['currency_id'] = $object->currency->slug;
            }
        }

        foreach (['weight_min', 'weight_max', 'volume_min', 'volume_max'] as $item) {
            if (isset($return[$item])) {
                $return[$item] = format_number($return[$item]);
            }
        }

        return $return;
    }

    /**
     * @param $object
     * @return array
     */
    protected function getReference($object): array
    {
        return [
            'boolean' => get_labels_boolean(),
            'currency' => get_labels(Currency::class),
            'role' => get_labels(Role::class),
        ];
    }
}
