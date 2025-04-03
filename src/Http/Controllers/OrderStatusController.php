<?php

namespace Netto\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Netto\Http\Requests\OrderStatusRequest as WorkRequest;
use Netto\Models\OrderStatus as WorkModel;
use Netto\Traits\CrudControllerActions;

class OrderStatusController extends Abstract\AdminCrudController
{
    use CrudControllerActions;

    protected string $class = WorkModel::class;
    protected string $id = 'status';

    protected array $list = [
        'relations' => [],
        'title' => 'cms-store::main.list_order_status',
        'url' => [
            'create',
            'delete',
        ],
    ];

    protected array $messages = [
        'create' => 'cms-store::main.create_order_status',
    ];

    protected array $route = [
        'index' => 'admin.order.index',
        'create' => 'admin.status.create',
        'delete' => 'admin.status.delete',
        'destroy' => 'admin.status.destroy',
        'edit' => 'admin.status.edit',
        'store' => 'admin.status.store',
        'update' => 'admin.status.update',
    ];

    protected string $title = 'cms-store::main.list_order_status';

    protected array $view = [
        'edit' => 'cms-store::order.status'
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
     * @param $object
     * @return array
     */
    protected function getReference($object): array
    {
        return [
            'boolean' => get_labels_boolean(),
        ];
    }
}
