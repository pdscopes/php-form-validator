<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateRequiredIfTest extends TestCase
{
    use ValidateTrait;

    public function testValidateRequiredIfValidTrue()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => 'username@example.com', 'field1' => 'baz'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredIfInvalidTrue()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => null, 'field1' => 'baz'];
        $errors = ['errors' => ['field0' => ['required-if' => 'Field0 is required if Field1 equals baz']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredIfValidFalse()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => null, 'field1' => 'qax'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
}