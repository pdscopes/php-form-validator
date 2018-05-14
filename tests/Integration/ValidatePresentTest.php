<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidatePresentTest extends TestCase
{
    use ValidateTrait;

    public function testValidatePresentValid()
    {
        $rules  = ['username' => 'present'];
        $values = ['username' => 'username@example.com'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidatePresentValidNull()
    {
        $rules  = ['username' => 'present'];
        $values = ['username' => null];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidatePresentInvalid()
    {
        $rules  = ['username' => 'present'];
        $values = [];
        $errors = ['errors' => ['username' => ['present' => 'Username must be present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    public function testValidatePresentValidDots()
    {
        $rules  = ['user.*.name' => 'present'];
        $values = ['user' => [['name' => 'username@example.com']]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidatePresentValidNullDots()
    {
        $rules  = ['user.*.name' => 'present'];
        $values = ['user' => [['name' => null]]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidatePresentInvalidDots()
    {
        $rules  = ['user.*.name' => 'present'];
        $values = [];
        $errors = ['errors' => ['user.*.name' => ['present' => 'User name must be present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
}