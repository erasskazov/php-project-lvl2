<?php

namespace Differ\Formatters\Json;

function buildAssoc(array $tree)
{
    return array_map(
        function ($node) {
            $status = array_key_exists('status', $node) ? $node['status'] : 'nest';
            if ($status === 'updated') {
                $before = $node['diff']['before'];
                $after = $node['diff']['after'];
                $beforeVal = array_key_exists('children', $before) ? buildAssoc($before['children']) : $before['value'];
                $afterVal = array_key_exists('children', $after) ? buildAssoc($after['children']) : $after['value'];
                return ['key' => $node['key'], 'status' => 'updated', 'before' => $beforeVal, 'after' => $afterVal];
            } elseif (array_key_exists('children', $node)) {
                return [$node['key'] => buildAssoc($node['children']), 'status' => $status];
            } else {
                return [$node['key'] => $node['value'], 'status' => $status];
            }
        },
        $tree
    );
}

function getJson(array $tree)
{
    $assoc = buildAssoc($tree);
    return json_encode($assoc, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
}
