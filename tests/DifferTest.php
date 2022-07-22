<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private string $path = __DIR__ . "/fixtures/Nested/";

    private function getFilePath($name)
    {
        return $this->path . $name;
    }

    protected function setUp(): void
    {
        $this->expectedStylish = file_get_contents($this->getFilePath("resultStylish.txt"));
        $this->expectedPlain = file_get_contents($this->getFilePath("resultPlain.txt"));
        $this->expectedJson = file_get_contents($this->getFilePath("resultJson.json"));
    }

    public function testStylish()
    {
        $this->assertEquals(
            $this->expectedStylish,
            genDiff($this->getFilePath("before.json"), $this->getFilePath("after.json"))
        );
        $this->assertEquals(
            $this->expectedStylish,
            genDiff($this->getFilePath("before.yaml"), $this->getFilePath("after.yaml"))
        );
    }

    public function testPlain()
    {
        $this->assertEquals(
            $this->expectedPlain,
            genDiff($this->getFilePath("before.json"), $this->getFilePath("after.json"), 'plain')
        );
        $this->assertEquals(
            $this->expectedPlain,
            genDiff($this->getFilePath("before.yaml"), $this->getFilePath("after.yaml"), 'plain')
        );
    }

    public function testJson()
    {
        $this->assertEquals(
            $this->expectedJson,
            genDiff($this->getFilePath("before.json"), $this->getFilePath("after.json"), 'json')
        );
        $this->assertEquals(
            $this->expectedJson,
            genDiff($this->getFilePath("before.yaml"), $this->getFilePath("after.yaml"), 'json')
        );
    }
}
