<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateMinStrLenTest extends TestCase
{
    use ValidateTrait;

    public function testValidateMinStrLenValid()
    {
        $rules  = ['field' => 'min-str-len:3'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateMinStrLenInvalid()
    {
        $rules  = ['field' => 'min-str-len:3'];
        $values = ['field' => 'ab'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}