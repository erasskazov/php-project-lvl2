<?php

namespace Differ\Formatters\Stylish;

use function Differ\Tree\Builder\toString;
use function Differ\Tree\Builder\hasChildren;
use function Differ\Tree\Builder\getChildren;
use function Differ\Tree\Builder\getStatus;
use function Differ\Tree\Builder\getValue;
use function Differ\Tree\Builder\getKey;
use function Differ\Tree\Builder\getDiff;

const INDENTS = array(
    'added' => '+ ',
    'removed' => "- ",
    'unchanged' => "  ",
    'blank' => "  "
);


function buildStylishTree($diff, $depth)
{
    $lines = array_map(
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
            return $intend . toString(getKey($node), getValue($node));
        },
        $diff
    );
    return implode("\n", $lines);
}

function getStylish($diff)
{
    return "{\n" . buildStylishTree($diff, 0) . "\n}";
}
