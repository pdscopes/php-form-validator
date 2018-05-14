<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateInTest extends TestCase
{
    use ValidateTrait;

    public function testValidateInValid()
    {
        $rules  = ['field' => 'in:alpha,beta,gamma'];
        $values = ['field' => 'beta'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateInInvalid()
    {
        $rules  = ['field' => 'in:alpha,beta,gamma'];
        $values = ['field' => 'omega'];
        $errors = ['errors' => ['field' => ['in' => 'Field must be one of: alpha, beta, gamma']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
}