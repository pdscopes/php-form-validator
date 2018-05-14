<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateEqualsTest extends TestCase
{
    use ValidateTrait;

    public function testValidateEqualsValid()
    {
        $rules  = ['confirm' => 'equals:password'];
        $values = ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateEqualsValidDots()
    {
        $rules  = ['field.*.confirm' => 'equals:field.*.password'];
        $values = [
            'field' => [
                ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'],
                ['password' => 1, 'confirm' => '1']
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateEqualsInvalid()
    {
        $rules  = ['confirm' => 'equals:password'];
        $values = ['password' => 'pa55w0rd', 'confirm' => 'password'];
        $errors = ['errors' => ['confirm' => ['equals' => 'Confirm must equal Password']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    public function testValidateEqualsInvalidDots()
    {
        $rules  = ['field.*.confirm' => 'equals:field.*.password'];
        $values = [
            'field' => [
                ['password' => 'pa55w0rd', 'confirm' => 'blah'],
                ['password' => 'blah', 'confirm' => 'pa55w0rd']
            ]
        ];
        $errors = ['errors' => [
            'field.0.confirm' => ['equals' => 'Field 0 confirm must equal Field 0 password'],
            'field.1.confirm' => ['equals' => 'Field 1 confirm must equal Field 1 password'],
        ]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
}