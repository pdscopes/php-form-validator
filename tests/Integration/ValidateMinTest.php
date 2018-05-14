<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateMinTest extends TestCase
{
    use ValidateTrait;

    public function testValidateMinValid()
    {
        $rules  = ['field' => 'min:5'];
        $values = ['field' => 5];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateMinInvalid()
    {
        $rules  = ['field' => 'min:5'];
        $values = ['field' => 4];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());

        $rules  = ['field' => 'min:5'];
        $values = ['field' => 0];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}