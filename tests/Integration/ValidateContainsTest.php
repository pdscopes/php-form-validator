<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateContainsTest extends TestCase
{
    use ValidateTrait;

    public function testValidateContainsValid()
    {
        $rules  = ['field' => 'contains:alpha,gamma'];
        $values = ['field' => ['alpha', 'beta', 'gamma']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateContainsInvalid()
    {
        $rules  = ['field' => 'contains:alpha,omega'];
        $values = ['field' => ['alpha', 'beta', 'gamma']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}