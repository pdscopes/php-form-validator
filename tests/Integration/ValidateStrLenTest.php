<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateStrLenTest extends TestCase
{
    use ValidateTrait;

    public function testValidateStrLenValid()
    {
        $rules  = ['field' => 'str-len:3'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateStrLenInvalid()
    {
        $rules  = ['field' => 'str-len:3'];
        $values = ['field' => 'abca'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}