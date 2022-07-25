<?php

namespace Differ\Formatters\Stylish;

const INDENTS = array(
    'added' => '+ ',
    'removed' => "- ",
    'unchanged' => "  ",
    'blank' => "  "
);

function buildStylishTree(array $diff, int $depth)
{
    $lines = array_map(
        function ($node) use ($depth) {
            $status = array_key_exists('status', $node) ? $node['status'] : 'blank';
            if ($status === 'updated') {
                return buildStylishTree($node['diff'], $depth);
            }
            $spaces = INDENTS['blank'] . str_repeat(' ', $depth * 4);
            $intend = $spaces . INDENTS[$status];
            $intendBracket = $spaces . INDENTS['blank'];
            if (array_key_exists('children', $node)) {
                return $intend . $node['key'] .
                    ": {\n" . buildStylishTree($node['children'], $depth + 1) .
                    "\n" . $intendBracket . "}" ;
            }
            return $intend . getFormattedValue($node['key'], $node['value']);
        },
        $diff
    );
    return implode("\n", $lines);
}

function getStylish(mixed $diff)
{
    return "{\n" . buildStylishTree($diff, 0) . "\n}";
}

function getFormattedValue(mixed $key, mixed $value)
{
    if (is_bool($value)) {
        return "{$key}: " . ($value ? 'true' : 'false');
    }
    $stringValue = $value ?? 'null';
    return "{$key}: {$stringValue}";
}
