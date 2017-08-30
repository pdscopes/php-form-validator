<?php

namespace MadeSimple\Validator\Test\Unit;

use MadeSimple\Validator\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function testDashesToCamel()
    {
        $this->assertEquals('Foo', Str::dashesToCamel('foo'));
        $this->assertEquals('FooBar', Str::dashesToCamel('foo-bar'));
        $this->assertEquals('FooBarBaz', Str::dashesToCamel('foo-bar-baz'));
    }

    public function testPrettyAttribute()
    {
        $this->assertEquals('Foo', Str::prettyAttribute('foo'));
        $this->assertEquals('Foo bar', Str::prettyAttribute('foo.bar'));
        $this->assertEquals('Foo bar', Str::prettyAttribute('foo.*.bar'));
        $this->assertEquals('Foo bar baz', Str::prettyAttribute('foo.bar.baz'));
        $this->assertEquals('Foo bar baz', Str::prettyAttribute('foo.*.bar.*.baz'));
    }

    public function testStrposX()
    {
        $this->assertEquals(3, Str::strposX('foo.*.bar', '.'));
        $this->assertEquals(5, Str::strposX('foo.*.bar', '.', 2));
        $this->assertFalse(Str::strposX('foo.*.bar', '.', 3));
    }

    public function testOverlapl()
    {
        $this->assertEquals('foo.*.ba', Str::overlapl('foo.*.bar', 'foo.*.baz'));
        $this->assertEquals('foo.*.ba', Str::overlapl('foo.*.baz', 'foo.*.bar'));
        $this->assertFalse(Str::overlapl('foo.*.bar', 'username'));
        $this->assertFalse(Str::overlapl('username', 'foo.*.bar'));
    }

    public function testLoverlaplMerge()
    {
        $pattern   = 'foo.*.bar';
        $field     = 'foo.*.baz';
        $attribute = 'foo.0.bar';
        $overlap   = Str::overlapl($pattern, $field);

        $this->assertEquals('foo.0.baz', Str::overlaplMerge($overlap, $attribute, $field));
    }
}