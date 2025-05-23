<?php

namespace Netto\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;

use Netto\Http\Controllers\Admin\Abstract\CrudController as BaseController;
use Netto\Traits\Crud\AdminActions;

use Netto\Models\Delivery as WorkModel;
use Netto\Http\Requests\Admin\DeliveryRequest as WorkRequest;

use Netto\Models\Permission;

class DeliveryController extends BaseController
{
    use AdminActions;

    protected string $baseRoute = 'store.delivery';
    protected string $className = WorkModel::class;

    protected array $crudTitle = [
        'list' => 'main.list_delivery',
        'create' => 'main.create_delivery',
    ];

    protected string $itemRouteId = 'delivery';

    protected array $syncRelations = ['permissions'];

    protected array $viewId = [
        'list' => 'cms::delivery.index',
        'edit' => 'cms::delivery.delivery',
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
     * @return WorkModel
     */
    protected function createModel(): WorkModel
    {
        $return = new $this->className();
        $return->setAttribute('currency_id', get_default_currency_id());

        return $return;
    }

    /**
     * @param $model
     * @return array
     */
    protected function getReference($model): array
    {
        return [
            'boolean' => get_labels_boolean(),
            'currency' => get_labels_currency(),
            'permission' => get_labels_translated(Permission::class),
        ];
    }
}
