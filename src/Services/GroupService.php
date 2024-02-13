<?php

namespace Netto\Services;

use App\Models\Group;

abstract class GroupService
{
    /**
     * @param int $id
     * @param array|null $list
     * @return array|null
     */
    public static function findNode(int $id, ?array $list = null): ?array
    {
        if (is_null($list)) {
            $list = self::getStructure();
        }

        if (array_key_exists($id, $list)) {
            return $list[$id];
        }

        foreach ($list as $item) {
            if ($item['kids'] && ($return = self::findNode($id, $item['kids']))) {
                return $return;
            }
        }

        return null;
    }

    /**
     * @param int|null $skipId
     * @param bool $emptyLead
     * @return array
     */
    public static function getLabels(?int $skipId = null, bool $emptyLead = true): array
    {
        $return = [];
        if ($emptyLead) {
            $return[''] = '';
        }

        foreach (self::_getLabels($skipId, self::getStructure()) as $id => $option) {
            $return[$id] = $option;
        }

        return $return;
    }

    /**
     * @return array
     */
    protected static function getStructure(): array
    {
        static $return;

        if (is_null($return)) {
            $list = [];
            foreach (Group::all() as $item) {
                $list[$item->id] = $item;
            }

            foreach ($list as $id => $item) {
                $item->kids = self::getKids($id, $list);
            }

            $return = [];
            foreach ($list as $id => $item) {
                if (is_null($item->parent_id)) {
                    $return[$id] = [
                        'id' => $id,
                        'name' => $item->name,
                        'parent_id' => $item->parent_id,
                        'slug' => $item->slug,
                        'kids' => self::pullKids($id, $list),
                    ];
                }
            }

            $return = self::setDepth($return);
        }

        return $return;
    }

    /**
     * @param int $id
     * @param array $list
     * @return array
     */
    protected static function getKids(int $id, array $list): array
    {
        $return = [];

        foreach ($list as $key => $value) {
            if ($value->parent_id == $id) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * @param int $id
     * @param array $list
     * @return array
     */
    protected static function pullKids(int $id, array $list): array
    {
        $return = [];

        foreach ($list[$id]->kids as $key => $value) {
            $return[$key] = [
                'id' => $key,
                'name' => $value->name,
                'parent_id' => $value->parent_id,
                'slug' => $value->slug,
                'kids' => self::pullKids($key, $list)
            ];
        }

        return $return;
    }

    /**
     * @param array $array
     * @param int $depth
     * @return array
     */
    protected static function setDepth(array $array, int $depth = 0): array
    {
        foreach ($array as $key => $value) {
            $array[$key]['depth'] = $depth;

            if ($value['kids']) {
                $depth++;
                $array[$key]['kids'] = self::setDepth($value['kids'], $depth);
                $depth--;
            }
        }

        return $array;
    }

    /**
     * @param int|null $skipId
     * @param array $list
     * @return array
     */
    private static function _getLabels(?int $skipId, array $list): array
    {
        $return = [];

        foreach ($list as $id => $item) {
            if ($id == $skipId) {
                continue;
            }

            $spacer = str_repeat('&nbsp;', ($item['depth'] * 3));
            $return[$id] = $spacer.$item['name'];

            if ($item['kids']) {
                foreach (self::_getLabels($skipId, $item['kids']) as $key => $value) {
                    $return[$key] = $value;
                }
            }
        }

        return $return;
    }
}
