<?php

namespace MadeSimple\Validator\Test\Integration;

use MadeSimple\Validator\Validate;
use PHPUnit\Framework\TestCase;

class ValidateRequiredWithAllTest extends TestCase
{
    use ValidateTrait;

    public function testValidateRequiredWithNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern () to field (*)");
        $this->expectException(\InvalidArgumentException::class);
        Validate::requiredWithAll($this->validator, [], '', '', ['*']);
    }

    public function testValidateRequiredWithValidTrue()
    {
        $rules  = ['current' => 'required-with-all:password,confirm'];
        $values = ['current' => 'oldPass', 'password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidTrue()
    {
        $rules  = ['current' => 'required-with-all:password,confirm'];
        $values = ['current' => null, 'password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'];
        $errors = ['errors' => ['current' => ['required-with-all' => 'Current is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    /**
     * @param array $values
     * @dataProvider validateRequiredWithValidFalseDataProvider
     */
    public function testValidateRequiredWithValidFalse($values)
    {
        $rules  = ['current' => 'required-with-all:password,confirm'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function validateRequiredWithValidFalseDataProvider()
    {
        return [
            [[]],
            [['password' => 'pa55w0rd']],
            [['confirm' => 'pa55w0rd']],

            [['current' => null]],
            [['current' => null, 'password' => 'pa55w0rd']],
            [['current' => null, 'confirm' => 'pa55w0rd']],
        ];
    }

    public function testValidateRequiredWithValidTrueDots()
    {
        $rules  = ['field.*.current' => 'required-with-all:field.*.password,field.*.confirm'];
        $values = [
            'field' => [
                [
                    'current' => 'oldPass',
                    'password' => 'pa55w0rd',
                    'confirm' => 'pa55w0rd',
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidTrueDots()
    {
        $rules  = ['field.*.current' => 'required-with-all:field.*.password,field.*.confirm'];
        $values = [
            'field' => [
                [
                    'current' => null,
                    'password' => 'pa55w0rd',
                    'confirm' => 'pa55w0rd',
                ]
            ]
        ];
        $errors = ['errors' => ['field.*.current' => ['required-with-all' => 'Field current is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    /**
     * @param array $values
     * @dataProvider validateRequiredWithValidFalseDotsDataProvider
     */
    public function testValidateRequiredWithValidFalseDots($values)
    {
        $rules  = ['foo.*.current' => 'required-with-all:foo.*.password,foo.*.confirm'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function validateRequiredWithValidFalseDotsDataProvider()
    {
        return [
            [[]],
            [['fields' => [['password' => 'pa55w0rd']]]],
            [['fields' => [['confirm' => 'pa55w0rd']]]],

            [['fields' => [['current' => null]]]],
            [['fields' => [['current' => null, 'password' => 'pa55w0rd']]]],
            [['fields' => [['current' => null, 'confirm' => 'pa55w0rd']]]],
        ];
    }
}