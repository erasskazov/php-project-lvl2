<?php

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private string $path = __DIR__ . "/fixtures/";

    private function getFilePath($name)
    {
        return $this->path . $name;
    }

    protected function setUp(): void
    {
        $this->expectedPlain = file_get_contents($this->getFilePath("result.txt"));
    }

    public function testPlain()
    {
        $this->assertEquals($this->expectedPlain, genDiff($this->getFilePath("before.json"), $this->getFilePath("after.json")));
    }
}