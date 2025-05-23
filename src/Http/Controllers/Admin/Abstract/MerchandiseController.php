<?php

namespace Netto\Http\Controllers\Admin\Abstract;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\Session;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request, Response};

use Netto\Http\Controllers\Admin\Abstract\CrudController as BaseController;

use App\Models\Merchandise as WorkModel;
use App\Http\Requests\Admin\MerchandiseRequest as WorkRequest;

use App\Models\Section;
use Netto\Models\Album;
use Netto\Exceptions\NettoException;

abstract class MerchandiseController extends BaseController
{
    protected string $baseRoute = 'store.merchandise';
    protected string $className = WorkModel::class;

    protected array $crudTitle = [
        'list' => 'main.navigation_group_store',
        'create' => 'main.create_merchandise',
    ];

    protected string $itemRouteId = 'merchandise';

    protected array $syncRelations = ['sections'];

    protected array $viewId = [
        'list' => 'cms::merchandise.index',
        'edit' => 'cms::merchandise.merchandise',
    ];

    /**
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $parentId = $request->get('parent');

        $model = $this->createModel($parentId);
        $model->loadCosts();

        $this->setAutoSort($model, $parentId);

        return $this->form($model, [
            'url' => [
                'index' => $this->getRouteEditParent($parentId),
                'save' => $this->getRouteStore($parentId),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $parentId = $request->get('parent');

        $model = $this->getModel($id);
        $model->delete();

        $status = session('status');
        if (is_null($status)) {
            $status = __('main.general_status_deleted');
            $route = $this->getRouteEditParent($parentId);
        } else {
            Session::forget('status');
            $route = $this->getRouteEdit($id, $parentId);
        }

        return redirect()->to($route)->with('status', $status);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function edit(Request $request, string $id): Response
    {
        $parentId = $request->get('parent');

        $model = $this->getModel($id);
        $model->loadCosts();

        return $this->form($model, [
            'url' => [
                'index' => $this->getRouteEditParent($parentId),
                'save' => $this->getRouteUpdate($model->getAttribute('id'), $parentId),
                'destroy' => $this->getRouteDestroy($model->getAttribute('id'), $parentId),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NettoException
     */
    public function list(Request $request): JsonResponse
    {
        $filter = [];

        if ($parentId = $request->query('parent')) {
            $filter['sections.section_id'] = $parentId;
        }

        $return = $this->getList($this->createModel($parentId), $filter);
        if ($parentId) {
            foreach ($return['items'] as $key => $value) {
                $return['items'][$key]['_editUrl'] = route($this->getRouteCrud('edit'), [
                    $this->itemRouteId => $value['id'],
                    'parent' => $parentId,
                ]);
            }
        }

        return response()->json($return);
    }

    /**
     * @param WorkRequest $request
     * @return RedirectResponse
     */
    public function store(WorkRequest $request): RedirectResponse
    {
        $model = $this->createModel($request->query('parent'));
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
     * @param Model|null $model
     * @return void
     * @throws NettoException
     */
    protected function addCrumbs(?Model $model = null): void
    {
        try {
            $parentId = request()->get('parent');
        } catch (\Throwable $throwable) {
            throw new NettoException($throwable->getMessage());
        }

        if ($parentId) {
            $parent = find_section_node($parentId);

            do {
                $this->addCrumb($parent['name'], $this->getRouteEditParent($parent['id']));
                $this->addTitle($parent['name']);

                $parent = $parent['parent_id'] ? find_section_node($parent['parent_id']) : null;
            } while ($parent);
        }

        $header = __($this->crudTitle['list']);

        $this->addTitle($header);
        $this->addCrumb($header, $this->getRouteIndex());
    }

    /**
     * @param string|null $parentId
     * @return WorkModel
     */
    protected function createModel(?string $parentId = null): WorkModel
    {
        /** @var WorkModel $return */
        $return = new $this->className();
        $return->setRelation('sections', $parentId
            ? Section::query()->where('id', $parentId)->get()
            : collect()
        );

        return $return;
    }

    /**
     * @param string $id
     * @return WorkModel
     */
    protected function getModel(string $id): WorkModel
    {
        return $this->createModel()->newQuery()->with('sections')->findOrFail($id);
    }

    /**
     * @param $model
     * @return array
     */
    protected function getReference($model): array
    {
        return [
            'albums' => get_labels(Album::class),
            'boolean' => get_labels_boolean(),
            'currency' => get_labels_currency(),
            'section' => get_labels_sections(null),
        ];
    }

    /**
     * @param string $id
     * @param string|null $parentId
     * @return string
     */
    protected function getRouteDestroy(string $id, ?string $parentId): string
    {
        return route($this->getRouteCrud('destroy'), [
            $this->itemRouteId => $id,
            'parent' => $parentId,
        ]);
    }

    /**
     * @param string $id
     * @param string|null $parentId
     * @return string
     */
    protected function getRouteEdit(string $id, ?string $parentId): string
    {
        return route($this->getRouteCrud('edit') , [
            $this->itemRouteId => $id,
            'parent' => $parentId,
        ]);
    }

    /**
     * @param null $parentId
     * @return string
     */
    protected function getRouteEditParent($parentId = null): string
    {
        if ($parentId && ($parent = find_section_node($parentId))) {
            return route($this->getRouteCrud('edit', 'store.section'), [
                'section' => $parent['id'],
                'parent' => $parent['parent_id'],
            ]);
        }

        return $this->getRouteIndex();
    }

    /**
     * @return string
     */
    protected function getRouteIndex(): string
    {
        return route($this->getRouteCrud('index'));
    }

    /**
     * @param string|null $parentId
     * @return string
     */
    protected function getRouteStore(?string $parentId = null): string
    {
        return route($this->getRouteCrud('store'), [
            'parent' => $parentId,
        ]);
    }

    /**
     * @param string $id
     * @param string|null $parentId
     * @return string
     */
    protected function getRouteUpdate(string $id, ?string $parentId): string
    {
        return route($this->getRouteCrud('update'), [
            $this->itemRouteId => $id,
            'parent' => $parentId,
        ]);
    }

    /**
     * @param WorkModel $model
     * @param $request
     * @return RedirectResponse
     */
    protected function redirect(WorkModel $model, $request): RedirectResponse
    {
        if (!$this->save($model, $request)) {
            return back()->with('status', __('main.error_saving_model'));
        }

        $parentId = $request->query('parent');
        $to = $request->get('button_apply')
            ? $this->getRouteEdit($model->getAttribute('id'), $parentId)
            : $this->getRouteEditParent($parentId);

        return redirect()->to($to)->with('status', __('main.general_status_saved'));
    }

    /**
     * @param WorkModel $model
     * @param string|null $parentId
     * @return void
     */
    protected function setAutoSort(WorkModel $model, ?string $parentId): void
    {
        $builder = $model->newQuery();

        if ($parentId) {
            $builder->whereHas('sections', function(Builder $builder) use ($parentId) {
                $builder->where('id', '=', $parentId);
            });
        }

        $model->setAttribute('sort', $builder->max('sort') + static::DEFAULT_SORT_STEP);
    }
}
