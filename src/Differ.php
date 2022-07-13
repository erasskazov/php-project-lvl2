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
    $json1 = json_decode(file_get_contents($pathToFile1), true);
    $json2 = json_decode(file_get_contents($pathToFile2), true);
    
    $jsonsKeys = array_unique([...array_keys($json1), ...array_keys($json2)]);
    sort($jsonsKeys);
    $diff = array_map(
        function ($key) use ($json1, $json2) {
            [$inJson1, $inJson2] = [array_key_exists($key, $json1), array_key_exists($key, $json2)]; 
            if (!$inJson2) {
                return ['key' => $key, 'value' => $json1[$key], 'status' => 'removed'];
            }
            if (!$inJson1) {
                return ['key' => $key, 'value' => $json2[$key], 'status' => 'added'];
            }
            if ($json1[$key] === $json2[$key]) {
                return ['key' => $key, 'value' => $json1[$key], 'status' => 'unchanged'];
            }
            return ['key' => $key, 'value' => ['json1' => $json1[$key], 'json2' => $json2[$key]], 'status' => 'updated'];
        },
        $jsonsKeys
    );
    return diffToStr($diff);
}

