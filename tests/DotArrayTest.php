<?php

namespace Blixon\tests;

use Blixon\DotArray\DotArray;
use Blixon\DotArray\IllegalAccessException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DotArray::class)]
class DotArrayTest extends TestCase
{
    public static function keyProvider(): array
    {
        return [
            ['foo.bar.baz', ['foo', 'bar', 'baz']],
            ['foo.', ['foo']],
            ['foo..', ['foo']],
            ['.bar', ['bar']],
            ['.bar.', ['bar']],
            ['.', []],
            ['..', []],
            ['', []]
        ];
    }

    #[DataProvider('keyProvider')]
    public function testSplittingKey(string $input, array $expected): void
    {
        $result = DotArray::splitKey($input);
        $this->assertEquals($expected, $result);
    }

    public static function settingProvider(): array
    {
        return [
            [
                [
                    'foo' => 'bar'
                ],
                ['foobar', 'baz'],
                [
                    'foo' => 'bar',
                    'foobar' => 'baz'
                ]
            ],
            [
                [
                    'foo' => 'bar'
                ],
                ['.foobar', 'baz'],
                [
                    'foo' => 'bar',
                    'foobar' => 'baz'
                ]
            ],
            [
                [
                    'foo' => 'bar'
                ],
                ['foobar.', 'baz'],
                [
                    'foo' => 'bar',
                    'foobar' => 'baz'
                ]
            ],
            [
                [
                    'foo' => [
                        'bar' => 'baz'
                    ]
                ],
                ['foo.foobar', 'foobaz'],
                [
                    'foo' => [
                        'bar' => 'baz',
                        'foobar' => 'foobaz'
                    ]
                ],
            ],
        ];
    }

    #[DataProvider('settingProvider')]
    public function testSetting(array $arr, array $toSet, array $expected): void
    {
        $arr = new DotArray($arr);
        [$key, $value] = $toSet;
        $arr[$key] = $value;
        $this->assertEquals($expected, $arr->getRawArray());
    }

    public static function illegalSettingProvider(): array
    {
        return [
            [''],
            [100],
            [null]
        ];
    }

    #[DataProvider('illegalSettingProvider')]
    public function testIllegalSetting(mixed $key): void
    {
        $this->expectException(IllegalAccessException::class);
        $arr = new DotArray();
        // 0 is arbitrary. Most important is the key.
        $arr[$key] = 0;
    }
}