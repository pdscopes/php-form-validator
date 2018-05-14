<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateUrlTest extends TestCase
{
    use ValidateTrait;

    public function testValidateUrlValid()
    {
        $rules  = ['field' => 'url'];
        $values = ['field' => 'https://github.com'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateUrlInvalid()
    {
        $rules  = ['field' => 'url'];
        $values = ['field' => 'username@github.com'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}