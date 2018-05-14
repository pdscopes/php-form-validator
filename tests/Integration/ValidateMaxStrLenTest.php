<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateMaxStrLenTest extends TestCase
{
    use ValidateTrait;

    public function testValidateMaxStrLenValid()
    {
        $rules  = ['field' => 'max-str-len:3'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateMaxStrLenInvalid()
    {
        $rules  = ['field' => 'max-str-len:3'];
        $values = ['field' => 'abcd'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}