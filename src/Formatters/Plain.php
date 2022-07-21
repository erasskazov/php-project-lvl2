<?php

namespace Differ\Formatters\Plain;

use function Differ\Trees\toString;
use function Differ\Trees\makeLeafNode;
use function Differ\Trees\makeDiffNode;
use function Differ\Trees\makeInternalNode;
use function Differ\Trees\hasChildren;
use function Differ\Trees\getChildren;
use function Differ\Trees\getStatus;
use function Differ\Trees\getValue;
use function Differ\Trees\getKey;
use function Differ\Trees\getBefore;
use function Differ\Trees\getAfter;
use function Differ\Trees\getMeta;
use function Differ\Trees\treeMap;
use function Differ\Trees\getDiff;
use function Differ\Trees\Builder\getPath;
use function Differ\Trees\isLeaf;
use function Differ\Trees\isInternal;

function valueToString($node)
{
    if (isInternal($node)) {
        return '[complex value]';
    }
    $value = getValue($node);
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return 'null';
    }
    return "'" . $value . "'";
}

function buildPlainDiff($tree)
{
    $result = array_reduce(
        $tree,
        function ($acc, $node) {
            if (getStatus($node) !== 'unchanged') {
                $path = implode('.', getMeta($node));
                if (getStatus($node) === 'updated') {
                    $before = getBefore($node);
                    $after = getAfter($node);
                    $valueBefore = isLeaf($before) ? valueToString($before) : '[complex value]';
                    $valueAfter = isLeaf($after) ? valueToString($after) : '[complex value]';
                    $string = "Property '{$path}' was updated. From {$valueBefore} to {$valueAfter}";
                } elseif (getStatus($node) === 'added') {
                    $value = isLeaf($node) ? valueToString($node) : '[complex value]';
                    $string = "Property '{$path}' was added with value: {$value}";
                } else {
                    $string = "Property '{$path}' was removed";
                }
                $acc[] = $string;
            } elseif (isInternal($node)) {
                $acc = [...$acc, ...buildPlainDiff(getChildren($node))];
            }
            return $acc;
        },
        []
    );
    return $result;
}


function getPlain($tree)
{
    $treeWithPaths = addPathsToMeta($tree);
    $plainDiff = buildPlainDiff($treeWithPaths);
    return implode("\n", $plainDiff);
}


function addPathsToMeta($tree)
{
    $iter = function ($tree, $path) use (&$iter) {
        return treeMap(
            function ($node) use ($iter, $path) {
                $path[] = getKey($node);
                if (isLeaf($node)) {
                    return makeLeafNode(getKey($node), getValue($node), getStatus($node), $path);
                }
                if (isInternal($node)) {
                    return makeInternalNode(getKey($node), $iter(getChildren($node), $path), getStatus($node), $path);
                }
                return makeDiffNode(getKey($node), getBefore($node), getAfter($node), $path);
            },
            $tree
        );
    };
    return $iter($tree, []);
}
