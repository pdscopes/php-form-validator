<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateMaxTest extends TestCase
{
    use ValidateTrait;

    public function testValidateMaxValid()
    {
        $rules  = ['field' => 'max:5'];
        $values = ['field' => 5];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateMaxInvalid()
    {
        $rules  = ['field' => 'max:5'];
        $values = ['field' => 6];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());

        $rules  = ['field' => 'max:-1'];
        $values = ['field' => 0];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}