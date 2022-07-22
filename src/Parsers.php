<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile)
{
    $fileContent = file_get_contents($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($extension === 'json') {
        return json_decode($fileContent);
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        return Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    throw new \Exception("Format {$extension} not support");
}
