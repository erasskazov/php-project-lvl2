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

function isLeaf(array $node)
{
    return $node['type'] === 'leaf';
}

function isInternal(array $node)
{
    return $node['type'] === 'internal';
}

function hasChildren(array $node)
{
    return is_array($node) && array_key_exists('children', $node);
}

function getChildren(array $node)
{
    return $node['children'];
}

function getStatus(array $node)
{
    return $node['status'];
}

function getValue(array $node)
{
    return $node['value'];
}

function getKey(array $node)
{
    return $node['key'];
}

function getDiff(array $node)
{
    return $node['children'];
}

function getBefore(array $node)
{
    return getDiff($node)['before'];
}

function getAfter(array $node)
{
    return getDiff($node)['after'];
}

function getMeta(array $node)
{
    return $node['meta'];
}

function makeLeafNode(mixed $key, mixed $value = null, string $status = 'unchanged', array $meta = [])
{
    return ['key' => $key, 'type' => 'leaf', 'value' => $value, 'status' => $status, 'meta' => $meta];
}

function makeInternalNode(mixed $key, array $children, string $status = 'unchanged', array $meta = [])
{
    return ['key' => $key, 'type' => 'internal', 'children' => $children, 'status' => $status, 'meta' => $meta];
}

function makeDiffNode(mixed $key, mixed $before, mixed $after, array $meta = [])
{
    return [
        'key' => $key,
        'type' => 'diff',
        'children' => ['before' => $before, 'after' => $after],
        'status' => 'updated',
        'meta' => $meta
    ];
}

function treeMap(callable $func, array $tree)
{
    return array_map($func, $tree);
}

function treeReduce(array $tree, callable $func, mixed $acc)
{
    return array_reduce($tree, $func, $acc);
}
