<?php

namespace MadeSimple\Validator\Test\Integration;

use MadeSimple\Validator\Validate;
use PHPUnit\Framework\TestCase;

class ValidateRequiredWithAnyTest extends TestCase
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
        $rules  = ['current' => 'required-with-any:password,confirm'];
        $values = ['current' => 'oldPass', 'password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    /**
     * @param array $values
     * @dataProvider validateRequiredWithInvalidTrueDataProvider
     */
    public function testValidateRequiredWithInvalidTrue($values)
    {
        $rules  = ['current' => 'required-with-any:password,confirm'];
        $errors = ['errors' => ['current' => ['required-with-any' => 'Current is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    public function validateRequiredWithInvalidTrueDataProvider()
    {
        return [
            [['current' => null, 'password' => 'pa55w0rd', 'confirm' => 'pa55w0rd']],
            [['current' => null, 'password' => 'pa55w0rd']],
            [['current' => null, 'confirm' => 'pa55w0rd']],
            [['password' => 'pa55w0rd']],
            [['confirm' => 'pa55w0rd']],
        ];
    }

    /**
     * @param array $values
     * @dataProvider validateRequiredWithValidFalseDataProvider
     */
    public function testValidateRequiredWithValidFalse($values)
    {
        $rules  = ['current' => 'required-with-any:password,confirm'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function validateRequiredWithValidFalseDataProvider()
    {
        return [
            [[]],
            [['current' => null]],
        ];
    }

    public function testValidateRequiredWithValidTrueDots()
    {
        $rules  = ['field.*.current' => 'required-with-any:field.*.password,field.*.confirm'];
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

    /**
     * @param array $values
     * @dataProvider validateRequiredWithInvalidTrueDotsDataProvider
     */
    public function testValidateRequiredWithInvalidTrueDots($values)
    {
        $rules  = ['field.*.current' => 'required-with-any:field.*.password,field.*.confirm'];
        $errors = ['errors' => ['field.*.current' => ['required-with-any' => 'Field current is required']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    public function validateRequiredWithInvalidTrueDotsDataProvider()
    {
        return [
            [['field' => [['current' => null, 'password' => 'pa55w0rd', 'confirm' => 'pa55w0rd']]]],
            [['field' => [['current' => null, 'password' => 'pa55w0rd']]]],
            [['field' => [['current' => null, 'confirm' => 'pa55w0rd']]]],
            [['field' => [['password' => 'pa55w0rd']]]],
            [['field' => [['confirm' => 'pa55w0rd']]]],
        ];
    }

    /**
     * @param array $values
     * @dataProvider validateRequiredWithValidFalseDotsDataProvider
     */
    public function testValidateRequiredWithValidFalseDots($values)
    {
        $rules  = ['foo.*.current' => 'required-with-any:foo.*.password,foo.*.confirm'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function validateRequiredWithValidFalseDotsDataProvider()
    {
        return [
            [[]],
            [['fields' => [[]]]],
            [['fields' => [['current' => null]]]],
        ];
    }
}