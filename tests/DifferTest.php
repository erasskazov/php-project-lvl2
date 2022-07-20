<?php

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private string $pathPlain = __DIR__ . "/fixtures/Plain/";
    private string $pathNested = __DIR__ . "/fixtures/Nested/";

    private function getFilePath($name, $type = 'plain')
    {
        return ($type === 'plain' ? $this->pathPlain : $this->pathNested) . $name;
    }

    protected function setUp(): void
    {
        $this->expectedPlain = file_get_contents($this->getFilePath("result.txt", 'plain'));
        $this->expectedNested = file_get_contents($this->getFilePath("result.txt", 'nested'));
    }

    public function testPlain()
    {
        $this->assertEquals($this->expectedPlain, genDiff($this->getFilePath("before.json"), $this->getFilePath("after.json")));
        $this->assertEquals($this->expectedPlain, genDiff($this->getFilePath("before.yml"), $this->getFilePath("after.yml")));
    }

    public function testNested()
    {
        $this->assertEquals($this->expectedNested, genDiff($this->getFilePath("before.json", 'nested'), $this->getFilePath("after.json", 'nested')));
        $this->assertEquals($this->expectedNested, genDiff($this->getFilePath("before.yaml", 'nested'), $this->getFilePath("after.yaml", 'nested')));
    }
}