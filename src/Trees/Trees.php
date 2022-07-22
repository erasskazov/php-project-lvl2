<?php

namespace Differ\Trees;

function toString(mixed $key, mixed $value): string
{
    if (is_bool($value)) {
        return "{$key}: " . ($value ? 'true' : 'false');
    }
    $stringValue = $value ?? 'null';
    return "{$key}: {$stringValue}";
}

function isLeaf($node)
{
    return $node['type'] === 'leaf';
}

function isInternal($node)
{
    return $node['type'] === 'internal';
}

function hasChildren($node)
{
    return is_array($node) && array_key_exists('children', $node);
}

function getChildren($node)
{
    return $node['children'];
}

function getStatus($node)
{
    return $node['status'];
}

function getValue($node)
{
    return $node['value'];
}

function getKey($node)
{
    return $node['key'];
}

function getDiff($node)
{
    return $node['children'];
}

function getBefore($node)
{
    return getDiff($node)['before'];
}

function getAfter($node)
{
    return getDiff($node)['after'];
}

function getMeta($node)
{
    return $node['meta'];
}

function makeLeafNode($key, $value = null, $status = 'unchanged', $meta = [])
{
    return ['key' => $key, 'type' => 'leaf', 'value' => $value, 'status' => $status, 'meta' => $meta];
}

function makeInternalNode($key, $children, $status = 'unchanged', $meta = [])
{
    return ['key' => $key, 'type' => 'internal', 'children' => $children, 'status' => $status, 'meta' => $meta];
}

function makeDiffNode($key, $before, $after, $meta = [])
{
    return [
        'key' => $key,
        'type' => 'diff',
        'children' => ['before' => $before, 'after' => $after],
        'status' => 'updated',
        'meta' => $meta
    ];
}

function treeMap($func, $tree)
{
    return array_map($func, $tree);
}

function treeReduce($tree, $func, $acc)
{
    return array_reduce($tree, $func, $acc);
}
