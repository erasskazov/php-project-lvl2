<?php

namespace Differ\Trees\Builder;

use function Differ\Trees\getKey;
use function Differ\Trees\getStatus;
use function Differ\Trees\getValue;
use function Differ\Trees\getChildren;
use function Differ\Trees\getBefore;
use function Differ\Trees\getAfter;
use function Differ\Trees\isInternal;
use function Differ\Trees\isLeaf;
use function Differ\Trees\makeInternalNode;
use function Differ\Trees\makeLeafNode;
use function Differ\Trees\makeDiffNode;
use function Differ\Trees\treeMap;

function buildTree($before, $after)
{
    $beforeKeys = array_keys(get_object_vars($before));
    $afterKeys = array_keys(get_object_vars($after));
    $unionKeys = array_unique([...$beforeKeys, ...$afterKeys]);
    sort($unionKeys);
    return array_map(
        function ($key) use ($before, $after) {
            [$beforeIsObject, $afterIsObject] = [
                property_exists($before, $key) && is_object($before->$key),
                property_exists($after, $key) && is_object($after->$key)
            ];

            if ($beforeIsObject && $afterIsObject) {
                return makeInternalNode($key, buildTree($before->$key, $after->$key));
            }

            if (!property_exists($after, $key)) {
                if ($beforeIsObject) {
                    return makeInternalNode($key, buildChildren($before->$key), 'removed');
                }
                return makeLeafNode($key, $before->$key, 'removed');
            }

            if (!property_exists($before, $key)) {
                if ($afterIsObject) {
                    return makeInternalNode($key, buildChildren($after->$key), 'added');
                }
                return makeLeafNode($key, $after->$key, 'added');
            }

            if ($before->$key === $after->$key) {
                if ($afterIsObject) {
                    return makeInternalNode($key, buildChildren($before->$key));
                }
                return makeLeafNode($key, $after->$key);
            }

            if ($beforeIsObject) {
                $before = makeInternalNode($key, buildChildren($before->$key), 'removed');
            } else {
                $before = makeLeafNode($key, $before->$key, 'removed');
            }

            if ($afterIsObject) {
                $after = makeInternalNode($key, buildChildren($after->$key), 'added');
            } else {
                $after = makeLeafNode($key, $after->$key, 'added');
            }
            return makeDiffNode($key, $before, $after);
        },
        $unionKeys
    );
}

function buildChildren($branch)
{
    $keys = array_keys((array) $branch);
    $result = array_map(
        function ($key) use ($branch) {
            if (!is_object($branch->$key)) {
                return makeLeafNode($key, $branch->$key);
            }
            return makeInternalNode($key, buildChildren($branch->$key));
        },
        $keys
    );
    return $result;
}
