<?php

namespace Blixon\tests;

use Blixon\DotArray\DotArray;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DotArray::class)]
class DotArrayTest extends TestCase
{
    public static function keyProvider(): array
    {
        return [
            ["foo.bar.baz", ["foo", "bar", "baz"]],
            ["foo.", ["foo"]],
            ["foo..", ["foo"]],
            [".bar", ["bar"]],
            [".bar.", ["bar"]],
            [".", []],
            ["..", []],
            ["", []]
        ];
    }

    #[DataProvider('keyProvider')]
    public function testSplittingKey(string $input, array $expected): void
    {
        $result = DotArray::splitKey($input);
        $this->assertEquals($expected, $result);
    }
}