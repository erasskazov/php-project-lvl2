<?php

namespace Differ\Formatters\Json;

use function Differ\Trees\getBefore;
use function Differ\Trees\getAfter;
use function Differ\Trees\getChildren;
use function Differ\Trees\getKey;
use function Differ\Trees\getValue;
use function Differ\Trees\getStatus;
use function Differ\Trees\isInternal;
use function Differ\Trees\isLeaf;
use function Differ\Trees\treeMap;
use function Differ\Trees\treeReduce;

function buildAssoc($tree)
{
    return treeReduce(
        $tree,
        function ($acc, $node) {
            $status = getStatus($node);
            if ($status === 'updated') {
                $before = getBefore($node);
                $after = getAfter($node);
                $beforeValue = isLeaf($before) ? getValue($before) : buildAssoc(getChildren($before));
                $afterValue = isLeaf($after) ? getValue($after) : buildAssoc(getChildren($after));
                return [...$acc, getKey($node) => ['before' => $beforeValue, 'after' => $afterValue]];
            }
            if (isInternal($node)) {
                return [...$acc, getKey($node) => buildAssoc(getChildren($node))];
            }
            return [...$acc, getKey($node) => getValue($node)];
        },
        []
    );
}

function getJson($tree)
{
    $assoc = buildAssoc($tree);
    return json_encode($assoc, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
}
