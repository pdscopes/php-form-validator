<?php

namespace MadeSimple\Validator\Test\Integration;

use MadeSimple\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = new Validator();
    }

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

    public function testValidateRequiredValid()
    {
        $rules  = ['username' => 'required'];
        $values = ['username' => 'username@example.com'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateRequiredValidDots()
    {
        $rules  = ['user.*.name' => 'required'];
        $values = ['user' => [['name' => 'username@example.com']]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredIfValidTrue()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => 'username@example.com', 'field1' => 'baz'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateRequiredIfValidFalse()
    {
        $rules  = ['field0' => 'required-if:field1,baz'];
        $values = ['field0' => null, 'field1' => 'qax'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithValidTrue()
    {
        $rules  = ['passwordConfirm' => 'required-with:password'];
        $values = ['password' => 'pa55w0rd', 'passwordConfirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateRequiredWithValidFalse()
    {
        $rules  = ['passwordConfirm' => 'required-with:password'];
        $values = [];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateRequiredWithValidTrueDots()
    {
        $rules  = ['field.*.confirm' => 'required-with:field.*.password'];
        $values = [
            'field' => [
                [
                    'password' => 'pa55w0rd',
                    'confirm' => 'pa55w0rd',
                ]
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateRequiredWithValidFalseDots()
    {
        $rules  = ['foo.*.confirm' => 'required-with:foo.*.password'];
        $values = [];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

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

    public function testValidateInValid()
    {
        $rules  = ['field' => 'in:alpha,beta,gamma'];
        $values = ['field' => 'beta'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateInInvalid()
    {
        $rules  = ['field' => 'in:alpha,beta,gamma'];
        $values = ['field' => 'omega'];
        $errors = ['errors' => ['field' => ['in' => 'Field must be: alpha, beta, gamma']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateContainsOnlyValid()
    {
        $rules  = ['field' => 'contains-only:alpha,beta,gamma'];
        $values = ['field' => ['alpha','gamma']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateContainsOnlyInvalid()
    {
        $rules  = ['field' => 'contains-only:alpha,beta,gamma'];
        $values = ['field' => ['alpha', 'gamma', 'omega']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testMinArrCountValid()
    {
        $rules  = ['field' => 'min-arr-count:2'];
        $values = ['field' => ['one', 'two']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testMinArrCountInvalid()
    {
        $rules  = ['field' => 'min-arr-count:2'];
        $values = ['field' => ['one']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testMaxArrCountValid()
    {
        $rules  = ['field' => 'max-arr-count:2'];
        $values = ['field' => ['one', 'two']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testMaxArrCountInvalid()
    {
        $rules  = ['field' => 'max-arr-count:2'];
        $values = ['field' => ['one', 'two', 'three']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateMinValid()
    {
        $rules  = ['field' => 'min:5'];
        $values = ['field' => 5];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateMinInvalid()
    {
        $rules  = ['field' => 'min:5'];
        $values = ['field' => 4];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateMaxValid()
    {
        $rules  = ['field' => 'max:5'];
        $values = ['field' => 5];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateMaxInvalid()
    {
        $rules  = ['field' => 'max:5'];
        $values = ['field' => 6];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

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

    public function testValidateLessThanValid()
    {
        $rules  = ['field0' => 'less-than:field1'];
        $values = ['field0' => 4, 'field1' => 5];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateLessThanInvalid()
    {
        $rules  = ['field0' => 'less-than:field1'];
        $values = ['field0' => 5, 'field1' => 5];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateMinStrLenValid()
    {
        $rules  = ['field' => 'min-str-len:3'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateMinStrLenInvalid()
    {
        $rules  = ['field' => 'min-str-len:3'];
        $values = ['field' => 'ab'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateMaxStrLenValid()
    {
        $rules  = ['field' => 'max-str-len:3'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateMaxStrLenInvalid()
    {
        $rules  = ['field' => 'max-str-len:3'];
        $values = ['field' => 'abcd'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateStrLenValid()
    {
        $rules  = ['field' => 'str-len:3'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateStrLenInvalid()
    {
        $rules  = ['field' => 'str-len:3'];
        $values = ['field' => 'abca'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateEmailValid()
    {
        $rules  = ['field' => 'email'];
        $values = ['field' => 'username@example.com'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateEmailInvalid()
    {
        $rules  = ['field' => 'email'];
        $values = ['field' => 'username'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateDateValid()
    {
        $rules  = ['field' => 'date'];
        $values = ['field' => '2017-08-29'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateDateInvalid()
    {
        $rules  = ['field' => 'date'];
        $values = ['field' => '2017-08-32'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateIsValid()
    {
        $rules  = ['field.*' => 'is:numeric'];
        $values = ['field' => [1, '2', 3, '4']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateIsInvalid()
    {
        $rules  = ['field' => 'is:numeric'];
        $values = ['field' => 'ab'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateAlphaValid()
    {
        $rules  = ['field' => 'alpha'];
        $values = ['field' => 'abc'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateAlphaInvalid()
    {
        $rules  = ['field' => 'alpha'];
        $values = ['field' => 'abc123'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateAlphaNumericValid()
    {
        $rules  = ['field' => 'alpha-numeric'];
        $values = ['field' => 'abc123'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateAlphaNumericInvalid()
    {
        $rules  = ['field' => 'alpha-numeric'];
        $values = ['field' => 'abc-123'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateUrlValid()
    {
        $rules  = ['field' => 'url'];
        $values = ['field' => 'https://github.com'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateUrlInvalid()
    {
        $rules  = ['field' => 'url'];
        $values = ['field' => 'username@github.com'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateCardNumberValid()
    {
        $rules  = ['field' => 'card-number'];
        $values = ['field' => '5105105105105100'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateCardNumberInvalid()
    {
        $rules  = ['field' => 'card-number'];
        $values = ['field' => '510510510510510'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }

    public function testValidateHumanNameValid()
    {
        $rules  = ['field' => 'human-name'];
        $values = ['field' => 'Joe Bloggs'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateHumanNameInvalid()
    {
        $rules  = ['field' => 'human-name'];
        $values = ['field' => 'Joe@Bloggs'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}