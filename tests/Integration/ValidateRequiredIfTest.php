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

    public function testValidateRequiredIfInvalidAndNotPresentTrue()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field1' => 'baz'];
        $errors = ['errors' => ['field0' => ['required-if' => 'Field0 is required if Field1 equals baz']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredIfValidNullFalse()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => null, 'field1' => null];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredIfValidNullAndNotPresentFalse()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field1' => null];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredIfValidUnequalFalse()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => null, 'field1' => 'qax'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithValidTrueDots()
    {
        $rules  = ['group.*.field1' => 'required-if:group.*.field0,foo'];
        $values = [
            'group' => [
                [
                    'field0' => 'foo',
                    'field1' => 'bar',
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidTrueDots()
    {
        $rules  = ['group.*.field1' => 'required-if:group.*.field0,foo'];
        $values = [
            'group' => [
                [
                    'field0' => 'foo',
                    'field1' => null,
                ]
            ]
        ];
        $errors = ['errors' => ['group.0.field1' => ['required-if' => 'Group 0 field1 is required if Group 0 field0 equals foo']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithInvalidAndNotPresentTrueDots()
    {
        $rules  = ['group.*.field1' => 'required-if:group.*.field0,foo'];
        $values = [
            'group' => [
                [
                    'field0' => 'foo',
                ]
            ]
        ];
        $errors = ['errors' => ['group.0.field1' => ['required-if' => 'Group 0 field1 is required if Group 0 field0 equals foo']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithValidFalseNullDots()
    {
        $rules  = ['group.*.field1' => 'required-if:group.*.field0,foo'];
        $values = [
            'group' => [
                [
                    'field0' => null,
                    'field1' => null,
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithValidFalseNullAndNotPresentDots()
    {
        $rules  = ['group.*.field1' => 'required-if:group.*.field0,foo'];
        $values = [
            'group' => [
                [
                    'field0' => null,
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithValidFalseUnequalDots()
    {
        $rules  = ['group.*.field1' => 'required-if:group.*.field0,foo'];
        $values = [
            'group' => [
                [
                    'field0' => 'bar',
                    'field1' => null,
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
}