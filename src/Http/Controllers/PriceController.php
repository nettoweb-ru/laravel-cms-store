<?php

namespace Netto\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Netto\Http\Requests\PriceRequest as WorkRequest;
use Netto\Models\Price as WorkModel;
use Netto\Models\Role;
use Netto\Traits\CrudControllerActions;

class PriceController extends Abstract\AdminCrudController
{
    use CrudControllerActions;

    protected string $class = WorkModel::class;
    protected string $id = 'price';

    protected array $list = [
        'relations' => [],
        'title' => 'cms-store::main.list_price',
        'url' => [
            'create',
            'delete',
        ],
    ];

    protected array $messages = [
        'create' => 'cms-store::main.create_price',
    ];

    protected array $route = [
        'index' => 'admin.price.index',
        'create' => 'admin.price.create',
        'delete' => 'admin.price.delete',
        'destroy' => 'admin.price.destroy',
        'edit' => 'admin.price.edit',
        'store' => 'admin.price.store',
        'update' => 'admin.price.update',
    ];

    protected array $sync = [
        'roles',
    ];

    protected string $title = 'cms-store::main.list_price';

    protected array $view = [
        'index' => 'cms-store::price.index',
        'edit' => 'cms-store::price.price'
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
            'role' => get_labels(Role::class),
        ];
    }
}
