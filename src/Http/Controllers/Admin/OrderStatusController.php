<?php

namespace Netto\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;

use Netto\Http\Controllers\Admin\Abstract\CrudController as BaseController;
use Netto\Traits\Crud\AdminActions;

use Netto\Models\OrderStatus as WorkModel;
use Netto\Http\Requests\Admin\OrderStatusRequest as WorkRequest;

class OrderStatusController extends BaseController
{
    use AdminActions;

    protected string $baseRoute = 'store.status';
    protected string $className = WorkModel::class;

    protected array $crudTitle = [
        'list' => 'main.list_status',
        'create' => 'main.create_status',
    ];

    protected string $itemRouteId = 'status';

    protected array $viewId = [
        'edit' => 'cms::order.status',
    ];

    /**
     * @param WorkRequest $request
     * @return RedirectResponse
     */
    public function store(WorkRequest $request): RedirectResponse
    {
        $model = $this->createModel();
        return $this->redirect($model, $request);
    }

    /**
     * @param WorkRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(WorkRequest $request, string $id): RedirectResponse
    {
        $model = $this->getModel($id);
        return $this->redirect($model, $request);
    }

    /**
     * @param $model
     * @return array
     */
    protected function getReference($model): array
    {
        return [
            'boolean' => get_labels_boolean(),
        ];
    }

    /**
     * @return string
     */
    protected function getRouteIndex(): string
    {
        return route($this->getRouteAdmin('store.order.index'));
    }
}
