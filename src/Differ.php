<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Tree\Builder\buildTree;
use function Differ\Formatters\Stylish\getStylish;

function genDiff(string $pathToFile1, string $pathToFile2, string $fmt = 'stylish')
{
    $before = parseFile($pathToFile1);
    $after = parseFile($pathToFile2);
    $tree = buildTree($before, $after);
    switch ($fmt) {
        case 'stylish':
            $result = getStylish($tree);
            break;
    }
    print_r($result);
    return $result;
}


