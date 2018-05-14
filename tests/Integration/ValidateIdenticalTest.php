<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateIdenticalTest extends TestCase
{
    use ValidateTrait;

    public function testValidateIdenticalValid()
    {
        $rules  = ['confirm' => 'identical:password'];
        $values = ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateIdenticalValidDots()
    {
        $rules  = ['field.*.confirm' => 'identical:field.*.password'];
        $values = [
            'field' => [
                ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'],
                ['password' => 1, 'confirm' => 1]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
}