<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getStylish;
use function Differ\Formatters\Plain\getPlain;
use function Differ\Formatters\Json\getJson;

function formatTree(mixed $tree, string $fmt)
{
    switch ($fmt) {
        case 'stylish':
            return getStylish($tree);
        case 'plain':
            return getPlain($tree);
        case 'json':
            return getJson($tree);
    }
}
