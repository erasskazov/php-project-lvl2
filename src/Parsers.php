<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile($pathToFile)
{
    $fileContent = file_get_contents($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($extension === 'json') {
        return json_decode($fileContent, true);
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        return Yaml::parse($fileContent);
    }
    throw new \Exception("Format {$extension} not support");
}

// var_dump();