<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateAlphaTest extends TestCase
{
    use ValidateTrait;

    public function testValidateAlphaValid()
    {
        $rules  = ['field' => 'alpha'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateAlphaInvalid()
    {
        $rules  = ['field' => 'alpha'];
        $values = ['field' => 'abc123'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}