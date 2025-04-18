<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\GroupRequest as WorkRequest;
use App\Models\Group as WorkModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Netto\Traits\CrudControllerGroupActions;
use Netto\Http\Controllers\Abstract;

class GroupController extends Abstract\AdminCrudController
{
    use CrudControllerGroupActions;

    protected bool $autoSort = true;
    protected string $class = WorkModel::class;
    protected string $id = 'group';

    protected array $list = [
        'relations' => [],
        'title' => 'cms-store::main.list_group',
        'url' => [
            'create',
            'delete',
            'toggle',
        ],
    ];

    protected array $messages = [
        'create' => 'cms-store::main.create_group',
    ];

    protected array $route = [
        'index' => 'admin.group.index',
        'create' => 'admin.group.create',
        'delete' => 'admin.group.delete',
        'destroy' => 'admin.group.destroy',
        'edit' => 'admin.group.edit',
        'store' => 'admin.group.store',
        'update' => 'admin.group.update',
        'toggle' => 'admin.group.toggle',
    ];

    protected string $title = 'cms-store::main.list_merchandise';

    protected array $view = [
        'index' => 'cms-store::merchandise.index',
        'edit' => 'admin.merchandise.group'
    ];

    /**
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $object = $this->getObject($this->class);

        $parentId = $request->get('parent');
        $object->setAttribute('parent_id', $parentId);

        $this->setRouteParams($parentId);
        $this->crumbs[] = [
            'title' => __($this->title),
            'link' => route(...$this->route['index']),
        ];

        if ($parentId) {
            $this->addCrumbParent($parentId);
        }

        return $this->_edit($object);
    }

    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $object = $this->getObject($this->class, [
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);

        $this->setRouteParams($object->parent_id);
        return $this->_destroy($object);
    }

    /**
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        $object = $this->getObject($this->class, [
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);

        $this->setRouteParams($object->parent_id);
        $this->crumbs[] = [
            'title' => __($this->title),
            'link' => route('admin.group.index'),
        ];

        if ($object->parent_id) {
            $this->addCrumbParent($object->parent_id);
        }

        return $this->_edit($object);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $parentId = $request->get('parent');
        $this->setRouteParams($parentId);

        if (!is_null($parentId)) {
            $this->list['title'] = '';
        }

        return $this->_list([
            'parent_id' => [
                'operator' => '=',
                'value' => $parentId,
            ],
        ]);
    }

    /**
     * @param WorkRequest $formRequest
     * @return RedirectResponse
     */
    public function store(WorkRequest $formRequest): RedirectResponse
    {
        $object = $this->getObject($this->class);
        $this->setRouteParams($formRequest->get('parent_id'));

        return $this->_save($formRequest, $object);
    }

    /**
     * @param WorkRequest $formRequest
     * @param string $id
     * @return RedirectResponse
     */
    public function update(WorkRequest $formRequest, string $id): RedirectResponse
    {
        $object = $this->getObject($this->class, [
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);
        $this->setRouteParams($object->parent_id);

        return $this->_save($formRequest, $object);
    }

    /**
     * @param Model $object
     * @return int
     */
    protected function getAutoSort(Model $object): int
    {
        return get_next_sort($object, [
            'parent_id' => [
                'operator' => '=',
                'value' => $object->parent_id,
            ]
        ]);
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

    /**
     * @param string|null $parentId
     * @return void
     */
    protected function setRouteParams(?string $parentId = null): void
    {
        foreach ($this->route as $key => $value) {
            $this->route[$key] = [
                'name' => $value,
                'parameters' => [],
            ];
        }

        if ($parentId) {
            $this->route['create']['parameters'] = ['parent' => $parentId];
            $this->route['index'] = [
                'name' => 'admin.group.edit',
                'parameters' => ['group' => $parentId],
            ];
        }
    }
}
