<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderRequest as WorkRequest;
use App\Models\Order as WorkModel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Netto\Models\Delivery;
use Netto\Models\OrderStatus;
use Netto\Traits\CrudControllerActions;
use Netto\Http\Controllers\Abstract;

class OrderController extends Abstract\AdminCrudController
{
    use CrudControllerActions;

    protected string $class = WorkModel::class;
    protected string $id = 'order';

    protected array $list = [
        'relations' => ['status', 'currency', 'user'],
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
        $return = parent::getItem($object);

        if ($object->currency && isset($return['total'])) {
            $return['total'] = format_currency($object->total, $object->currency->slug);
        }

        if (isset($return['status_id'])) {
            $return['status_id'] = "[{$object->status_id}] {$object->status->name}";
        }

        if (isset($return['user_id'])) {
            $return['user_id'] = $object->user ? "[{$object->user_id}] {$object->user->name}" : '';
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
            'status' => get_labels(OrderStatus::class),
            'user' => get_labels(User::class, true),
            'delivery' => get_labels(Delivery::class)
        ];
    }
}
