<?php

namespace MadeSimple\Validator\Test\Integration;

use MadeSimple\Validator\Validate;
use PHPUnit\Framework\TestCase;

class ValidateRequiredWithoutTest extends TestCase
{
    use ValidateTrait;

    public function testValidateRequiredWithoutNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern () to field (*)");
        $this->expectException(\InvalidArgumentException::class);
        Validate::requiredWithout($this->validator, [], '', '', ['*']);
    }

    public function testValidateRequiredWithoutValidTrue()
    {
        $rules  = ['field1' => 'required-without:field0'];
        $values = ['field0' => null, 'field1' => 'value1'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithoutInvalidTrue()
    {
        $rules  = ['field1' => 'required-without:field0'];
        $values = ['field0' => null, 'field1' => null];
        $errors = ['errors' => ['field1' => ['required-without' => 'Field1 is required when Field0 is not present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithoutValidFalse()
    {
        $rules  = ['field1' => 'required-without:field0'];
        $values = ['field0' => 'value0'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithoutValidTrueDots()
    {
        $rules  = ['field.*.field1' => 'required-without:field.*.field0'];
        $values = [
            'field' => [
                [
                    'field0' => null,
                    'field1' => 'value1',
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithoutInvalidTrueDots()
    {
        $rules  = ['field.*.field1' => 'required-without:field.*.field0'];
        $values = [
            'field' => [
                [
                    'field0' => null,
                    'field1' => null,
                ]
            ]
        ];
        $errors = ['errors' => ['field.0.field1' => ['required-without' => 'Field 0 field1 is required when Field 0 field0 is not present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithoutValidFalseDots()
    {
        $rules  = ['foo.*.field1' => 'required-without:foo.*.field0'];
        $values = ['foo' => [['field0' => 'value0']]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }


    public function testValidateRequiredWithoutValidReciprocalTrue()
    {
        $rules  = [
            'field1' => 'required-without:field0',
            'field0' => 'required-without:field1',
        ];

        $values = ['field0' => null, 'field1' => 'value1'];
        $this->validator->validate($values, $rules);
        $this->assertFalse($this->validator->hasErrors());

        $values = ['field0' => 'value0', 'field1' => null];
        $this->validator->validate($values, $rules);
        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithoutInvalidReciprocalTrue()
    {
        $rules  = [
            'field1' => 'required-without:field0',
            'field0' => 'required-without:field1',
        ];
        $values = ['field0' => null, 'field1' => null];
        $errors = [
            'errors' => [
                'field0' => ['required-without' => 'Field0 is required when Field1 is not present'],
                'field1' => ['required-without' => 'Field1 is required when Field0 is not present'],
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithoutValidReciprocalFalse()
    {
        $rules  = [
            'field1' => 'required-without:field0',
            'field0' => 'required-without:field1',
        ];
        $values = ['field0' => 'value0', 'field1' => 'value1'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithoutValidReciprocalTrueDots()
    {
        $rules  = [
            'user.*.field0' => 'required-without:user.*.field1',
            'user.*.field1' => 'required-without:user.*.field0',
        ];

        $values = ['user' => [
            ['field0' => 'value0', 'field1' => 'value1'],
            ['field0' => 'value0', 'field1' => 'value1'],
        ]];
        $this->validator->validate($values, $rules);
        $this->assertFalse($this->validator->hasErrors());
    }


    public function testValidateRequiredWithoutValidChainTrue()
    {
        $rules  = [
            'alpha' => 'required-without:beta',
            'beta'  => 'required-without:gamma',
            'gamma' => 'required-without:alpha',
        ];
        $values = ['alpha' => 'one', 'beta' => 'two', 'gamma' => 'three'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
}