<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateAlphaNumericTest extends TestCase
{
    use ValidateTrait;

    public function testValidateAlphaNumericValid()
    {
        $rules  = ['field' => 'alpha-numeric'];
        $values = ['field' => 'abc123'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateAlphaNumericInvalid()
    {
        $rules  = ['field' => 'alpha-numeric'];
        $values = ['field' => 'abc-123'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}