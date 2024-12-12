<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderRequest as WorkRequest;
use App\Models\Order as WorkModel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Netto\Models\Delivery;
use Netto\Models\OrderStatus;
use Netto\Services\CmsService;
use Netto\Traits\CrudControllerActions;
use Netto\Http\Controllers\Abstract;

class OrderController extends Abstract\AdminCrudController
{
    use CrudControllerActions;

    protected string $class = WorkModel::class;
    protected string $id = 'order';

    protected array $list = [
        'columns' => [
            'id' => [
                'title' => 'cms-store::main.attr_order_number',
                'width' => 5
            ],
            'created_at' => [
                'title' => 'cms::main.attr_created_at',
                'width' => 25
            ],
            'total' => [
                'title' => 'cms-store::main.attr_total',
                'width' => 20
            ],
            'user_id' => [
                'title' => 'cms::main.attr_user',
                'width' => 30
            ],
            'status_id' => [
                'title' => 'cms-store::main.attr_status_id',
                'width' => 20
            ],
        ],
        'relations' => ['status', 'currency', 'user'],
        'select' => [
            'id',
            'created_at',
            'total',
            'currency_id',
            'status_id',
            'user_id',
        ],
        'title' => 'cms-store::main.list_order',
        'url' => [
            'delete',
        ],
    ];

    protected array $messages = [
        'edit' => 'cms-store::main.edit_order',
    ];

    protected array $route = [
        'index' => 'admin.order.index',
        'delete' => 'admin.order.delete',
        'destroy' => 'admin.order.destroy',
        'edit' => 'admin.order.edit',
        'store' => 'admin.order.store',
        'update' => 'admin.order.update',
    ];

    protected string $title = 'cms-store::main.list_order';

    protected array $view = [
        'index' => 'cms-store::order.index',
        'edit' => 'admin.order.order'
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
            'created_at' => format_date($object->created_at),
            'total' => format_currency($object->total, $object->currency->slug),
            'status_id' => "[{$object->status_id}] {$object->status->name}",
            'user_id' => $object->user ? "[{$object->user_id}] {$object->user->name}" : '',
        ];
    }

    /**
     * @param $object
     * @return array
     */
    protected function getReference($object): array
    {
        return [
            'status' => CmsService::getModelLabels(OrderStatus::class),
            'user' => CmsService::getModelLabels(User::class, 'name', true),
            'delivery' => CmsService::getModelLabels(Delivery::class)
        ];
    }
}
