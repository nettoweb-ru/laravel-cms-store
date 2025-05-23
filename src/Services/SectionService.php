<?php

namespace Netto\Services;

use App\Models\Section;

abstract class SectionService
{
    /**
     * Return section node by ID.
     *
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
     * Returns associative array [$id => indented $name] for section list.
     *
     * @param int|null $skipId
     * @param bool $emptyLead
     * @param array|null $list
     * @return array
     */
    public static function getLabels(?int $skipId, bool $emptyLead = false, ?array $list = null): array
    {
        $return = [];
        if ($emptyLead) {
            $return[''] = '';
        }

        if (is_null($list)) {
            $list = self::getStructure();
        }

        foreach ($list as $id => $item) {
            if ($id == $skipId) {
                continue;
            }

            $spacer = str_repeat('&nbsp;', ($item['depth'] * 3));
            $return[$id] = $spacer.$item['name'];

            if ($item['kids']) {
                foreach (self::getLabels($skipId, false, $item['kids']) as $key => $value) {
                    $return[$key] = $value;
                }
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public static function getStructure(): array
    {
        static $return;
        if (is_null($return)) {
            $list = [];
            foreach (Section::with('translated')->get() as $item) {
                $list[$item->id] = $item;
            }

            foreach ($list as $id => $item) {
                $item->kids = self::getKids($id, $list);
            }

            $return = [];
            foreach ($list as $id => $item) {
                /** @var Section $item */
                if (is_null($item->getAttribute('parent_id'))) {
                    $return[$id] = [
                        'id' => $id,
                        'name' => $item->getTranslated('name')[get_default_language_code()],
                        'parent_id' => $item->getAttribute('parent_id'),
                        'slug' => $item->getAttribute('slug'),
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
    private static function getKids(int $id, array $list): array
    {
        $return = [];

        foreach ($list as $key => $value) {
            /** @var Section $value */
            if ($value->getAttribute('parent_id') == $id) {
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
    private static function pullKids(int $id, array $list): array
    {
        $return = [];

        foreach ($list[$id]->kids as $key => $value) {
            /** @var Section $value */
            $return[$key] = [
                'id' => $key,
                'name' => $value->getTranslated('name')[get_default_language_code()],
                'parent_id' => $value->getAttribute('parent_id'),
                'slug' => $value->getAttribute('slug'),
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
    private static function setDepth(array $array, int $depth = 0): array
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
}
