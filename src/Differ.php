<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;

function toString(mixed $key, mixed $value): string
{
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    }
    return "{$key}: {$value}";
}

function diffToStr($diff)
{
    $lines = array_map(
        function ($item) {
            if ($item['status'] === 'updated') {
                $strItemJson1 = toString($item['key'], $item['value']['before']);
                $strItemJson2 = toString($item['key'], $item['value']['after']);
                return "  - {$strItemJson1}\n  + {$strItemJson2}";
            }
            $strItemJson = toString($item['key'], $item['value']);
            if ($item['status'] === 'removed') {
                return "  - {$strItemJson}";
            }
            if ($item['status'] === 'added') {
                return "  + {$strItemJson}";
            }
            return "    {$strItemJson}";
        },
        $diff
    );
    return implode("\n", ['{', ...$lines, '}']);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $fmt = 'stylish')
{
    $before = parseFile($pathToFile1);
    $after = parseFile($pathToFile2);
    $keys = array_unique([...array_keys($before), ...array_keys($after)]);
    sort($keys);
    $diff = array_map(
        function ($key) use ($before, $after) {
            if (!array_key_exists($key, $after)) {
                return [
                    'key' => $key,
                    'value' => $before[$key],
                    'status' => 'removed'
                ];
            }
            if (!array_key_exists($key, $before)) {
                return [
                    'key' => $key,
                    'value' => $after[$key],
                    'status' => 'added'
                ];
            }
            if ($before[$key] === $after[$key]) {
                return ['key' => $key, 'value' => $before[$key], 'status' => 'unchanged'
                ];
            }
            return [
                'key' => $key,
                'value' => [
                    'before' => $before[$key],
                    'after' => $after[$key]
                ],
                'status' => 'updated'
            ];
        },
        $keys
    );
    return diffToStr($diff);
}
