<?php

namespace Netto\Http\Controllers;

use App\Http\Requests\MerchandiseRequest as WorkRequest;
use App\Models\Group;
use App\Models\Merchandise as WorkModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Netto\Models\Cost;
use Netto\Models\Currency;
use Netto\Services\CmsService;
use Netto\Services\CurrencyService;
use Netto\Services\GroupService;
use Netto\Services\PriceService;
use Netto\Traits\CrudControllerGroupActions;

class MerchandiseController extends Abstract\AdminCrudController
{
    use CrudControllerGroupActions;

    protected bool $autoSort = true;
    protected string $class = WorkModel::class;
    protected string $id = 'merchandise';

    protected array $list = [
        'columns' => [
            'sort' => [
                'title' => 'cms::main.attr_sort',
                'width' => 5
            ],
            'name' => [
                'title' => 'cms::main.attr_name',
                'width' => 75
            ],
            'slug' => [
                'title' => 'cms-store::main.attr_vendor_code',
                'width' => 20
            ],
        ],
        'params' => [
            'page' => 1,
            'perPage' => 10,
            'sort' => 'sort',
            'sortDir' => 'asc',
        ],
        'relations' => [],
        'select' => [
            'id',
            'name',
            'slug',
            'is_active',
            'sort',
        ],
        'title' => 'cms-store::main.list_merchandise',
        'url' => [
            'create',
            'delete',
            'toggle',
        ],
    ];

    protected array $messages = [
        'create' => 'cms-store::main.create_merchandise',
    ];

    protected array $route = [
        'index' => 'admin.group.index',
        'create' => 'admin.merchandise.create',
        'delete' => 'admin.merchandise.delete',
        'destroy' => 'admin.merchandise.destroy',
        'edit' => 'admin.merchandise.edit',
        'store' => 'admin.merchandise.store',
        'update' => 'admin.merchandise.update',
        'toggle' => 'admin.merchandise.toggle',
    ];

    protected array $sheets = [
        'edit' => ['merchandise_sheet'],
    ];

    protected array $sync = ['groups'];

    protected string $title = 'cms-store::main.list_merchandise';

    protected array $view = [
        'edit' => 'cms-store::merchandise.merchandise'
    ];

    /**
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        /** @var WorkModel $object */
        $parentId = $request->get('parent');
        $object = $this->getObject($this->class);

        $this->setRouteParams($parentId);
        $this->addCrumbIndex();

        if ($parentId) {
            $object->setAttribute('groups', Group::where('id', $parentId)->get());
            $this->addCrumbParent($parentId);
        }

        return $this->_edit($object, [
            'costs' => $this->getCosts($object)
        ]);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(string $id, Request $request): RedirectResponse
    {
        $object = $this->getObject($this->class, [
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);

        $this->setRouteParams($request->get('parent'));
        return $this->_destroy($object);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return View
     */
    public function edit(string $id, Request $request): View
    {
        /** @var WorkModel $object */
        $object = $this->getObject($this->class, [
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);

        $parentId = $request->get('parent');

        $this->setRouteParams($parentId);
        $this->addCrumbIndex();

        if ($parentId) {
            $this->addCrumbParent($parentId);
        }

        return $this->_edit($object, [
            'costs' => $this->getCosts($object)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $parentId = $request->get('parent');
        $this->setRouteParams($parentId);

        $filter = [];
        if (!is_null($parentId)) {
            $this->list['title'] = '';
            $filter = [
                'groups.id' => [
                    'operator' => '=',
                    'value' => $parentId,
                ],
            ];
        }

        return $this->_list($filter);
    }

    /**
     * @param WorkRequest $formRequest
     * @return RedirectResponse
     */
    public function store(WorkRequest $formRequest): RedirectResponse
    {
        /** @var WorkModel $object */
        $object = $this->getObject($this->class);
        $this->setRouteParams($formRequest->get('parent'));

        return $this->_save($formRequest, $object);
    }

    /**
     * @param WorkRequest $formRequest
     * @param string $id
     * @return RedirectResponse
     */
    public function update(WorkRequest $formRequest, string $id): RedirectResponse
    {
        /** @var WorkModel $object */
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
     * @param WorkRequest $request
     * @param WorkModel $model
     * @return RedirectResponse
     */
    protected function _save($request, $model): RedirectResponse
    {
        $validated = $request->validated();

        $attributes = [];
        $costs = [];

        foreach ($validated as $key => $value) {
            if (str_starts_with($key, 'costs|')) {
                $costs[$key] = $value;
            } else {
                $attributes[$key] = $value;
            }
        }

        if (!$model->saveMultiLang($attributes)) {
            return back()->with('status', __('cms::main.error_saving_model'));
        }

        foreach ($this->sync as $item) {
            $model->{$item}()->sync($request->get($item, []));
        }

        if (!$this->saveCosts($costs, $model)) {
            return back()->with('status', __('cms::main.error_saving_model'));
        }

        $model->refresh();
        return $this->redirect(empty($request->get('button_apply')), $model->id);
    }

    /**
     * @param WorkModel $object
     * @return int
     */
    protected function getAutoSort($object): int
    {
        $filter = [];

        if (count($object->groups)) {
            $group = $object->groups->get(0);
            $filter = [
                'groups.id' => [
                    'operator' => '=',
                    'value' => [$group->id],
                ],
            ];
        }

        return CmsService::getModelSort($object, $filter);
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
            foreach (['create', 'edit', 'store', 'update'] as $item) {
                $this->route[$item]['parameters'] = ['parent' => $parentId];
            }

            $this->route['index'] = [
                'name' => 'admin.group.edit',
                'parameters' => ['group' => $parentId],
            ];
        }
    }

    /**
     * @param $object
     * @return array
     */
    protected function getItem($object): array
    {
        return [
            'name' => $object->name,
            'sort' => $object->sort,
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
            'group' => GroupService::getLabels(null, false),
        ];
    }

    /**
     * @param WorkModel $object
     * @return array
     */
    private function getCosts(WorkModel $object): array
    {
        $return = [];
        $defaultId = CurrencyService::getDefaultId();

        foreach (PriceService::getList() as $item) {
            $return[$item['id']] = [
                'name' => $item['name'],
                'currency_id' => $defaultId,
                'value' => null,
            ];
        }

        foreach ($object->costs->all() as $item) {
            $return[$item->id]['currency_id'] = $item->pivot->currency_id;
            $return[$item->id]['value'] = $item->pivot->value;
        }

        return $return;
    }

    /**
     * @param array $input
     * @param WorkModel $object
     * @return bool
     */
    private function saveCosts(array $input, WorkModel $object): bool
    {
        $costs = [];
        foreach ($input as $key => $value) {
            $tmp = explode('|', $key);
            $costs[$tmp[1]][$tmp[2]] = $value;
        }

        $costModels = [];
        foreach ($object->costs->all() as $item) {
            $costModels[$item->id] = $item->pivot;
        }

        foreach (PriceService::getList() as $price) {
            if (!array_key_exists($price['id'], $costModels)) {
                $costModel = new Cost();
                $costModel->setAttribute('price_id', $price['id']);
                $costModel->setAttribute('merchandise_id', $object->id);

                $costModels[$price['id']] = $costModel;
            }
        }

        foreach ($costs as $priceId => $cost) {
            foreach ($cost as $key => $value) {
                $costModels[$priceId]->setAttribute($key, $value);
            }

            if (!$costModels[$priceId]->save()) {
                return false;
            }
        }

        return true;
    }
}
