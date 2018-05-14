<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateIsTest extends TestCase
{
    use ValidateTrait;

    public function testValidateIsValid()
    {
        $rules  = ['field.*' => 'is:numeric'];
        $values = ['field' => [1, '2', 3, '4']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateIsInvalid()
    {
        $rules  = ['field' => 'is:numeric'];
        $values = ['field' => 'ab'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}