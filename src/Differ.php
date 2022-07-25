<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Formatters\formatTree;
use function Functional\sort as functional_sort;

function genDiff(string $pathToFile1, string $pathToFile2, string $fmt = 'stylish')
{
    $before = parseFile($pathToFile1);
    $after = parseFile($pathToFile2);
    $tree = buildTree($before, $after);
    $result = formatTree($tree, $fmt);
    return $result;
}


function buildTree(object $before, object $after)
{
    $beforeKeys = array_keys(get_object_vars($before));
    $afterKeys = array_keys(get_object_vars($after));
    $unionKeys = array_unique([...$beforeKeys, ...$afterKeys]);
    $sortedKeys = functional_sort($unionKeys, fn($a, $b) => $a <=> $b);
    return array_map(
        function ($key) use ($before, $after) {

            if (!property_exists($after, $key)) {
                return buildChildren($key, $before->$key, 'removed');
            }

            if (!property_exists($before, $key)) {
                return buildChildren($key, $after->$key, 'added');
            }

            if ($before->$key === $after->$key) {
                return buildChildren($key, $before->$key, 'unchanged');
            }

            if (is_object($before->$key) && is_object($after->$key)) {
                return ['key' => $key, 'status' => 'unchanged', 'children' => buildTree($before->$key, $after->$key)];
            }

            return [
                'key' => $key,
                'status' => 'updated',
                'diff' => [
                    'before' => buildChildren($key, $before->$key, 'removed'),
                    'after' => buildChildren($key, $after->$key, 'added')
                ]
            ];
        },
        $sortedKeys
    );
}

function buildChildren(mixed $key, mixed $branch, string $status)
{
    if (!is_object($branch)) {
        return ['key' => $key, 'status' => $status, 'value' => $branch];
    }
    $iter = function ($node) use (&$iter) {
        $keys = array_keys(get_object_vars($node));
        $result = array_map(
            function ($key) use ($node, $iter) {
                if (!is_object($node->$key)) {
                    return ['key' => $key, 'value' => $node->$key];
                }
                return ['key' => $key, 'children' => $iter($node->$key)];
            },
            $keys
        );
        return $result;
    };
    return ['key' => $key, 'status' => $status, 'children' => $iter($branch)];
}
