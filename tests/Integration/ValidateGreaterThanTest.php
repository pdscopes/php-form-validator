<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateGreaterThanTest extends TestCase
{
    use ValidateTrait;

    public function testValidateGreaterThanValid()
    {
        $rules  = ['field0' => 'greater-than:field1'];
        $values = ['field0' => 6, 'field1' => 5];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateGreaterThanInvalid()
    {
        $rules  = ['field0' => 'greater-than:field1'];
        $values = ['field0' => 5, 'field1' => 5];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}