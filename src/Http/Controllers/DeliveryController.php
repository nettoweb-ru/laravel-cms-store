<?php

namespace Netto\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Netto\Http\Requests\DeliveryRequest as WorkRequest;
use Netto\Models\Currency;
use Netto\Models\Delivery as WorkModel;
use Netto\Models\Role;
use Netto\Services\CmsService;
use Netto\Traits\CrudControllerActions;

class DeliveryController extends Abstract\AdminCrudController
{
    use CrudControllerActions;

    protected string $class = WorkModel::class;
    protected string $id = 'delivery';

    protected array $list = [
        'columns' => [
            'sort' => [
                'title' => 'cms::main.attr_sort',
                'width' => 5
            ],
            'name' => [
                'title' => 'cms::main.attr_name',
                'width' => 55
            ],
            'cost' => [
                'title' => 'cms-store::main.attr_cost',
                'width' => 20
            ],
            'slug' => [
                'title' => 'cms::main.attr_slug',
                'width' => 20
            ],
        ],
        'params' => [
            'page' => 1,
            'perPage' => 10,
            'sort' => 'sort',
            'sortDir' => 'asc',
        ],
        'relations' => ['currency'],
        'select' => [
            'id',
            'sort',
            'name',
            'slug',
            'cost',
            'currency_id',
            'is_active',
        ],
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
        return [
            'sort' => $object->sort,
            'name' => $object->name,
            'cost' => format_currency($object->cost, $object->currency->slug),
            'slug' => $object->slug,
            'is_active' => $object->is_active,
        ];
    }

    /**
     * @param $object
     * @return array
     */
    protected function getReference($object): array
    {
        return [
            'boolean' => CmsService::getBooleanLabels(),
            'currency' => CmsService::getModelLabels(Currency::class),
            'role' => CmsService::getModelLabels(Role::class),
        ];
    }
}
