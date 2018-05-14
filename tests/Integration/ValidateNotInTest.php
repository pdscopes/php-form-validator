<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateNotInTest extends TestCase
{
    use ValidateTrait;

    public function testValidateNotInValid()
    {
        $rules  = ['field' => 'not-in:alpha,beta,gamma'];
        $values = ['field' => 'omega'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateNotInInvalid()
    {
        $rules  = ['field' => 'not-in:alpha,beta,gamma'];
        $values = ['field' => 'beta'];
        $errors = ['errors' => ['field' => ['not-in' => 'Field must not be one of: alpha, beta, gamma']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
}