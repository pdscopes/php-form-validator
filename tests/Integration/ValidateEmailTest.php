<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateEmailTest extends TestCase
{
    use ValidateTrait;

    public function testValidateEmailValid()
    {
        $rules  = ['field' => 'email'];
        $values = ['field' => 'username@example.com'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateEmailInvalid()
    {
        $rules  = ['field' => 'email'];
        $values = ['field' => 'username'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}