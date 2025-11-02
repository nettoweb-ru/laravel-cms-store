<?php

namespace Netto\Http\Controllers\Admin\Abstract;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\Session;
use Illuminate\Http\{JsonResponse, RedirectResponse, Response, Request};

use Netto\Http\Controllers\Admin\Abstract\CrudController as BaseController;

use App\Models\Section as WorkModel;
use App\Http\Requests\Admin\SectionRequest as WorkRequest;

use Netto\Models\Album;
use Netto\Exceptions\NettoException;

abstract class SectionController extends BaseController
{
    protected string $baseRoute = 'store.section';
    protected string $className = WorkModel::class;

    protected array $crudTitle = [
        'list' => 'main.navigation_group_store',
        'create' => 'main.create_section',
    ];

    protected string $itemRouteId = 'section';

    protected array $viewId = [
        'edit' => 'cms::merchandise.section',
    ];

    /**
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $parentId = $request->query('parent');

        $model = $this->createModel($parentId);
        $this->setAutoSort($model, $parentId);

        return $this->form($model, [
            'url' => [
                'index' => $model->parent
                    ? $this->getRouteEdit($model->parent->getAttribute('id'), $model->parent->getAttribute('parent_id'))
                    : $this->getRouteIndex(),
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
        $parentId = $request->query('parent');

        $model = $this->getModel($id, $parentId);
        $model->delete();

        $status = session('status');
        if (is_null($status)) {
            $status = __('main.general_status_deleted');
            $route = $model->parent
                ? $this->getRouteEdit($model->parent->getAttribute('id'), $model->parent->getAttribute('parent_id'))
                : $this->getRouteIndex();
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
        $model = $this->getModel($id, $request->query('parent'));

        return $this->form($model, [
            'url' => [
                'index' => $model->parent
                    ? $this->getRouteEdit($model->parent->getAttribute('id'), $model->parent->getAttribute('parent_id'))
                    : $this->getRouteIndex(),
                'save' => $this->getRouteUpdate($model->getAttribute('id'), $model->getAttribute('parent_id')),
                'destroy' => $this->getRouteDestroy($model->getAttribute('id'), $model->getAttribute('parent_id')),
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
        $return = $this->getListArray($request);
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
        $model = $this->getModel($id, $request->query('parent'));
        return $this->redirect($model, $request);
    }

    /**
     * @param Model|null $model
     * @return void
     */
    protected function addCrumbs(?Model $model = null): void
    {
        if ($model->getAttribute('parent_id')) {
            /** @var Model $parent */
            $parent = find_section_node($model->getAttribute('parent_id'));

            do {
                $this->addCrumb($parent['name'], $this->getRouteEdit($parent['id'], $parent['parent_id']));
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
    protected function createModel(?string $parentId): WorkModel
    {
        /** @var WorkModel $return */
        $return = new $this->className();

        if ($parentId) {
            $relation = $return->parent();

            /** @var Model $parent */
            $parent = $relation->getRelated()->newQuery()->findOrFail($parentId);
            $return->setAttribute($relation->getForeignKeyName(), $parentId);
            $return->setRelation('parent', $parent);
        }

        return $return;
    }

    /**
     * @param Request $request
     * @return array
     * @throws NettoException
     */
    protected function getListArray(Request $request): array
    {
        $parentId = $request->get('parent');

        return $this->getList(
            $this->createModel($parentId),
            array_merge([
                "parent_id" => [
                    'value' => $parentId,
                    'strict' => true,
                ]
            ], $this->getListFilter($request))
        );
    }

    /**
     * @param string $id
     * @param string|null $parentId
     * @return WorkModel
     */
    protected function getModel(string $id, ?string $parentId): WorkModel
    {
        return $this->createModel($parentId)->newQuery()->with('parent')->findOrFail($id);
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
            'parent' => get_labels_sections($model->getAttribute('id'), true),
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
            'parent' => $parentId
        ]);
    }

    /**
     * @param string $id
     * @param string|null $parentId
     * @return string
     */
    protected function getRouteEdit(string $id, ?string $parentId): string
    {
        return route($this->getRouteCrud('edit'), [
            $this->itemRouteId => $id,
            'parent' => $parentId
        ]);
    }

    /**
     * @return string
     */
    protected function getRouteIndex(): string
    {
        return route($this->getRouteCrud('index', 'store.merchandise'));
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

        $to = $request->get('button_apply')
            ? $this->getRouteEdit($model->getAttribute('id'), $model->getAttribute('parent_id'))
            : $this->getRouteIndex();

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
            $builder->whereHas('parent', function(Builder $builder) use ($parentId) {
                $builder->where('id', $parentId);
            });
        }

        $model->setAttribute('sort', $builder->max('sort') + static::DEFAULT_SORT_STEP);
    }
}
