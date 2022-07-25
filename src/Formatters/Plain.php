<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function getFormattedValue(mixed $node)
{
    if (array_key_exists('children', $node)) {
        return '[complex value]';
    }
    $value = $node['value'];
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return 'null';
    }
    if (is_numeric($value)) {
        return $value;
    }
    return "'" . $value . "'";
}

function buildPlainDiff(array $tree, string $ancestors)
{
    $lines = array_map(
        function ($node) use ($ancestors) {
            $path = $ancestors === '' ? "{$node['key']}" : "{$ancestors}.{$node['key']}";
            if (!array_key_exists('status', $node) || $node['status'] === 'unchanged') {
                if (array_key_exists('children', $node)) {
                    return buildPlainDiff($node['children'], $path);
                }
                return [];
            }

            if ($node['status'] === 'added') {
                return "Property '{$path}' was added with value: " . getFormattedValue($node);
            }

            if ($node['status'] === 'removed') {
                return "Property '{$path}' was removed";
            }

            return "Property '{$path}' was updated. From " . getFormattedValue($node['diff']['before']) .
                " to " . getFormattedValue($node['diff']['after']);
        },
        $tree
    );
    return flatten($lines);
}

function getPlain(array $tree)
{
    $plainDiff = buildPlainDiff($tree, '');
    return implode("\n", $plainDiff);
}
