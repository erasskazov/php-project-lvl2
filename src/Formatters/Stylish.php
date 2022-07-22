<?php

namespace Differ\Formatters\Stylish;

use function Differ\Trees\toString;
use function Differ\Trees\hasChildren;
use function Differ\Trees\getChildren;
use function Differ\Trees\getStatus;
use function Differ\Trees\getValue;
use function Differ\Trees\getKey;
use function Differ\Trees\getDiff;
use function Differ\Trees\treeMap;

const INDENTS = array(
    'added' => '+ ',
    'removed' => "- ",
    'unchanged' => "  ",
    'blank' => "  "
);


function buildStylishTree(mixed $diff, int $depth)
{
    $lines = treeMap(
        function ($node) use ($depth) {
            $status = getStatus($node);
            if ($status === 'updated') {
                return buildStylishTree(getDiff($node), $depth);
            }
            $spaces = INDENTS['blank'] . str_repeat(' ', $depth * 4);
            $intend = $spaces . INDENTS[$status];
            $intendBracket = $spaces . INDENTS['blank'];
            if (hasChildren($node)) {
                return $intend . getKey($node) .
                    ": {\n" . buildStylishTree(getChildren($node), $depth + 1) .
                    "\n" . $intendBracket . "}" ;
            }
            $key = getKey($node);
            return $intend . toString(getKey($node), getValue($node));
        },
        $diff
    );
    return implode("\n", $lines);
}

function getStylish(mixed $diff)
{
    return "{\n" . buildStylishTree($diff, 0) . "\n}";
}
