<?php

namespace Netto\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;

use Netto\Http\Controllers\Admin\Abstract\CrudController as BaseController;
use Netto\Traits\Crud\AdminActions;

use Netto\Models\Price as WorkModel;
use Netto\Http\Requests\Admin\PriceRequest as WorkRequest;

use Netto\Models\Permission;

class PriceController extends BaseController
{
    use AdminActions;

    protected string $baseRoute = 'store.price';
    protected string $className = WorkModel::class;

    protected array $crudTitle = [
        'list' => 'main.list_price',
        'create' => 'main.create_price',
    ];

    protected string $itemRouteId = 'price';

    protected array $syncRelations = ['permissions'];

    protected array $viewId = [
        'list' => 'cms::price.index',
        'edit' => 'cms::price.price',
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
            'permission' => get_labels_translated(Permission::class),
        ];
    }
}
