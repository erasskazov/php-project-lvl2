<?php

namespace Differ\Tree\Builder;

function toString(mixed $key, mixed $value): string
{
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    }
    $value = $value ?? 'null';
    return "{$key}: {$value}";
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
    if (array_key_exists('status', $node)) {
        return $node['status'];
    }
    return 'unchanged';
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
    return $node['diff'];
}

function buildTree($before, $after)
{
    $unionKeys = array_keys([...((array) $before), ...((array) $after)]);
    sort($unionKeys);
    return array_map(
        function ($key) use ($before, $after) {
            [$beforeIsObject, $afterIsObject] = [
                property_exists($before, $key) && is_object($before->$key),
                property_exists($after, $key) && is_object($after->$key)
            ];

            if ($beforeIsObject && $afterIsObject) {
                return ['key' => $key, 'children' => buildTree($before->$key, $after->$key)];
            }

            if (!property_exists($after, $key)) {
                if ($beforeIsObject) {
                    return ['key' => $key, 'status' => 'removed', 'children' => buildChildren($before->$key)];
                }
                return ['key' => $key, 'status' => 'removed', 'value' => $before->$key];
            }

            if (!property_exists($before, $key)) {
                if ($afterIsObject) {
                    return ['key' => $key, 'status' => 'added', 'children' => buildChildren($after->$key)];
                }
                return ['key' => $key, 'status' => 'added', 'value' => $after->$key];
            }

            if ($before->$key === $after->$key) {
                if ($afterIsObject) {
                    return ['key' => $key, 'children' => buildChildren($before->$key)];
                }
                return ['key' => $key, 'value' => $after->$key];
            }

            if ($beforeIsObject) {
                $before = ['key' => $key, 'children' => buildChildren($before->$key), 'status' => 'removed'];
            } else {
                $before = ['key' => $key, 'value' => $before->$key, 'status' => 'removed'];
            }

            if ($afterIsObject) {
                $after = ['key' => $key, 'children' => buildChildren($after->$key), 'status' => 'added'];
            } else {
                $after = ['key' => $key, 'value' => $after->$key, 'status' => 'added'];
            }
            return [
                'diff' => [$before, $after],
                'status' => 'updated'
            ];
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
                return ['key' => $key, 'value' => $branch->$key];
            }
            return ['key' => $key, 'children' => buildChildren($branch->$key)];
        },
        $keys
    );
    return $result;
}
