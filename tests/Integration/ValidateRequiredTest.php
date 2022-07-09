<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateRequiredTest extends TestCase
{
    use ValidateTrait;

    public function testValidateRequiredValid()
    {
        $rules  = ['roles.*' => 'required'];
        $values = ['roles'   => ['admin']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredInvalid()
    {
        $rules  = ['roles.*' => 'required'];
        $values = ['username' => 'username@example.com'];
        $errors = ['errors' => ['roles.*' => ['required' => 'Roles are required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredInvalidNull()
    {
        $rules  = ['username' => 'required'];
        $values = ['username' => null];
        $errors = ['errors' => ['username' => ['required' => 'Username is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredInvalidEmpty()
    {
        $rules  = ['roles' => 'required'];
        $values = ['roles' => []];
        $errors = ['errors' => ['roles' => ['required' => 'Roles is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredValidDots()
    {
        $rules  = ['user.*.name' => 'required'];
        $values = ['user' => [['name' => 'username@example.com']]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredInvalidDots()
    {
        $rules  = ['user.*.name' => 'required'];
        $values = ['user' => []];
        $errors = ['errors' => ['user.*.name' => ['required' => 'User name is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredInvalidNullDots()
    {
        $rules  = ['user.*.name' => 'required'];
        $values = ['user' => [['name' => null]]];
        $errors = ['errors' => ['user.0.name' => ['required' => 'User 0 name is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    
    public function testValidateRequiredInvalidEmptyDots()
    {
        $rules  = ['user.*.roles' => 'required'];
        $values = ['user' => [['roles' => []]]];
        $errors = ['errors' => ['user.0.roles' => ['required' => 'User 0 roles is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
}