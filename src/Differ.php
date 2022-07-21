<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Trees\Builder\buildTree;
use function Differ\Formatters\formatTree;

function genDiff(string $pathToFile1, string $pathToFile2, string $fmt = 'stylish')
{
    $before = parseFile($pathToFile1);
    $after = parseFile($pathToFile2);
    $tree = buildTree($before, $after);
    $result = formatTree($tree, $fmt);
    // print_r($result);
    return $result;
}
