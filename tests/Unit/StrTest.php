<?php

namespace MadeSimple\Validator\Test\Unit;

use MadeSimple\Validator\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    /**
     * @param string $expected
     * @param string $attribute
     * @dataProvider prettyAttributeProvider
     */
    public function testPrettyAttribute($expected, $attribute)
    {
        $this->assertEquals($expected, Str::prettyAttribute($attribute));
    }
    public function prettyAttributeProvider()
    {
        return [
            ['Foo', 'foo'],
            ['Foo bar', 'foo.bar'],
            ['Foo bar', 'foo.*.bar'],
            ['Foo bar baz', 'foo.bar.baz'],
            ['Foo bar baz', 'foo.*.bar.*.baz'],
            ['Foo bar', 'foo_bar'],
            ['Foo bar baz', 'foo_bar_baz'],
        ];
    }

    /**
     * @param string $expected
     * @param string $a
     * @param string $b
     * @dataProvider overlapLeftProvider
     */
    public function testOverlapLeft($expected, $a, $b)
    {
        $this->assertEquals($expected, Str::overlapLeft($a, $b));
    }
    public function overlapLeftProvider()
    {
        return [
            ['foo.*', 'foo.*.bar', 'foo.*.baz'],
            ['foo.*', 'foo.*.baz', 'foo.*.bar'],
            ['*', '*.alpha', '*.beta'],

            [false, 'foo.*.bar', 'username'],
            [false, 'username', 'foo.*.bar'],
            [false, 'field', 'fields.*'],
            [false, 'field', 'fields'],
        ];
    }

    /**
     * @param string $pattern
     * @param string $attribute
     * @param string $field
     * @param string $expected
     * @dataProvider leftOverlapMergeProvider
     */
    public function testOverlapLeftMerge($pattern, $attribute, $field, $expected)
    {
        $overlap = Str::overlapLeft($pattern, $field);

        $this->assertEquals($expected, Str::overlapLeftMerge($overlap, $attribute, $field));
    }
    public function leftOverlapMergeProvider()
    {
        return [
            ['foo.*.bar', 'foo.0.bar', 'foo.*.baz', 'foo.0.baz'],
            ['*.alpha', '15.alpha', '*.beta', '15.beta'],
        ];
    }
}