<?php

namespace Netto\Traits;

use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Netto\Services\GroupService;

trait CrudControllerGroupActions
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $id = $request->get('id', []);
        if (empty($id)) {
            abort(400);
        }

        return $this->_delete([
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function toggle(Request $request): JsonResponse
    {
        $id = $request->get('id', []);
        if (empty($id)) {
            abort(400);
        }

        return $this->_toggle([
            'id' => [
                'operator' => '=',
                'value' => $id,
            ],
        ]);
    }

    /**
     * @param string|null $parentId
     * @return void
     */
    protected function addCrumbParent(?string $parentId): void
    {
        $node = GroupService::findNode($parentId);
        if (is_null($node)) {
            return;
        }

        $crumbs = [$node['id'] => $node['name']];
        $parentId = $node['parent_id'];

        while ($parentId) {
            $parent = GroupService::findNode($parentId);
            $crumbs[$parent['id']] = $parent['name'];
            $parentId = $parent['parent_id'];
        }

        foreach (Group::whereIn('id', array_keys($crumbs))->get() as $model) {
            $params = ['group' => $model->id];
            if ($model->parent_id) {
                $params['parent'] = $model->parent_id;
            }

            $crumbs[$model->id] = [
                'title' => $model->name,
                'link' => route('admin.group.edit', $params),
            ];
        }

        foreach (array_reverse($crumbs) as $crumb) {
            $this->crumbs[] = $crumb;
        }
    }
}
