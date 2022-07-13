<?php

namespace Differ\Differ;

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
                $strItemJson1 = toString($item['key'], $item['value']['json1']);
                $strItemJson2 = toString($item['key'], $item['value']['json2']);
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

function genDiff(string $pathToFile1, string $pathToFile2)
{
    $jsonBefore = json_decode(file_get_contents($pathToFile1), true);
    $jsonAfter = json_decode(file_get_contents($pathToFile2), true);

    $jsonsKeys = array_unique([...array_keys($jsonBefore), ...array_keys($jsonAfter)]);
    sort($jsonsKeys);
    $diff = array_map(
        function ($key) use ($jsonBefore, $jsonAfter) {
            if (!array_key_exists($key, $jsonAfter)) {
                return [
                    'key' => $key,
                    'value' => $jsonBefore[$key],
                    'status' => 'removed'
                ];
            }
            if (!array_key_exists($key, $jsonBefore)) {
                return [
                    'key' => $key,
                    'value' => $jsonAfter[$key],
                    'status' => 'added'
                ];
            }
            if ($jsonBefore[$key] === $jsonAfter[$key]) {
                return ['key' => $key, 'value' => $jsonBefore[$key], 'status' => 'unchanged'
                ];
            }
            return [
                'key' => $key,
                'value' => [
                    'json1' => $jsonBefore[$key],
                    'json2' => $jsonAfter[$key]
                ],
                'status' => 'updated'
            ];
        },
        $jsonsKeys
    );
    return diffToStr($diff);
}
