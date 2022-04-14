<?php

namespace MadeSimple\Validator\Test\Integration;

use MadeSimple\Validator\Validate;
use PHPUnit\Framework\TestCase;

class ValidateRequiredWithTest extends TestCase
{
    use ValidateTrait;

    public function testValidateRequiredWithNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern () to field (*)");
        $this->expectException(\InvalidArgumentException::class);
        Validate::requiredWith($this->validator, [], '', '', ['*']);
    }

    public function testEqualsWithNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern () to field (*)");
        $this->expectException(\InvalidArgumentException::class);
        Validate::equals($this->validator, [], '', '', ['*']);
    }

    public function testNotEqualsWithNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern to field");
        $this->expectException(\InvalidArgumentException::class);
        Validate::notEquals($this->validator, [], '', '', ['*']);
    }

    public function testIdenticalWithNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern to field");
        $this->expectException(\InvalidArgumentException::class);
        Validate::identical($this->validator, [], '', '', ['*']);
    }

    public function testNotIdenticalWithNonMatchedPattern()
    {
        $this->expectExceptionMessage("Cannot match pattern to field");
        $this->expectException(\InvalidArgumentException::class);
        Validate::notIdentical($this->validator, [], '', '', ['*']);
    }

    public function testValidateRequiredWithValidTrue()
    {
        $rules  = ['passwordConfirm' => 'required-with:password'];
        $values = ['password' => 'pa55w0rd', 'passwordConfirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidTrue()
    {
        $rules  = ['passwordConfirm' => 'required-with:password'];
        $values = ['password' => 'pa55w0rd', 'passwordConfirm' => null];
        $errors = ['errors' => ['passwordConfirm' => ['required-with' => 'PasswordConfirm is required when Password is present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
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

    public function testValidateRequiredWithInvalidTrueDots()
    {
        $rules  = ['field.*.confirm' => 'required-with:field.*.password'];
        $values = [
            'field' => [
                [
                    'password' => 'pa55w0rd',
                    'confirm' => null,
                ]
            ]
        ];
        $errors = ['errors' => ['field.0.confirm' => ['required-with' => 'Field 0 confirm is required when Field 0 password is present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithValidFalseDots()
    {
        $rules  = ['foo.*.confirm' => 'required-with:foo.*.password'];
        $values = [];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }


    public function testValidateRequiredWithValidReciprocalTrue()
    {
        $rules  = [
            'confirm' => 'required-with:password',
            'password' => 'required-with:confirm',
        ];
        $values = ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidReciprocalTrue()
    {
        $rules  = [
            'confirm' => 'required-with:password',
            'password' => 'required-with:confirm',
        ];
        $values = ['password' => null, 'confirm' => 'pa55w0rd'];
        $errors = ['errors' => ['password' => ['required-with' => 'Password is required when Confirm is present']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithValidReciprocalFalse()
    {
        $rules  = [
            'confirm' => 'required-with:password',
            'password' => 'required-with:confirm',
        ];
        $values = ['password' => null, 'confirm' => null];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithValidReciprocalTrueDots()
    {
        $rules  = [
            'user.*.password' => 'required-with:user.*.confirm',
            'user.*.confirm' => 'required-with:user.*.password',
        ];
        $values = ['user' => [
            ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'],
            ['password' => 'pa55w0rd', 'confirm' => 'pa55w0rd'],
        ]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidReciprocalTrueDots()
    {
        $rules  = [
            'user.*.password' => 'required-with:user.*.confirm',
            'user.*.confirm' => 'required-with:user.*.password',
        ];
        $values = ['user' => [
            ['password' => null, 'confirm' => 'pa55w0rd'],
            ['password' => 'pa55w0rd', 'confirm' => null],
        ]];
        $errors = ['errors' => [
            'user.0.password' => ['required-with' => 'User 0 password is required when User 0 confirm is present'],
            'user.1.confirm' => ['required-with' => 'User 1 confirm is required when User 1 password is present'],
        ]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithValidReciprocalFalseDots()
    {
        $rules  = [
            'user.*.password' => 'required-with:user.*.confirm',
            'user.*.confirm' => 'required-with:user.*.password',
        ];
        $values = ['user' => [
            ['password' => null, 'confirm' => null],
            ['password' => null, 'confirm' => null],
        ]];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }


    public function testValidateRequiredWithValidChainTrue()
    {
        $rules  = [
            'alpha' => 'required-with:beta',
            'beta'  => 'required-with:gamma',
            'gamma' => 'required-with:alpha',
        ];
        $values = ['alpha' => 'one', 'beta' => 'two', 'gamma' => 'three'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidChainTrue()
    {
        $rules  = [
            'alpha' => 'required-with:beta',
            'beta'  => 'required-with:gamma',
            'gamma' => 'required-with:alpha',
        ];
        $values = [
            ['alpha' => null, 'beta' => 'two', 'gamma' => 'three'],
            ['alpha' => 'one', 'beta' => null, 'gamma' => 'three'],
            ['alpha' => 'one', 'beta' => 'two', 'gamma' => null],
        ];
        $errors = [
            ['errors' => ['alpha' => ['required-with' => 'Alpha is required when Beta is present']]],
            ['errors' => ['beta' => ['required-with' => 'Beta is required when Gamma is present']]],
            ['errors' => ['gamma' => ['required-with' => 'Gamma is required when Alpha is present']]],
        ];
        foreach (array_keys($values) as $k) {
            $this->validator->clear()->validate($values[$k], $rules);

            $this->assertTrue($this->validator->hasErrors());
            $this->assertEquals($errors[$k], $this->validator->getProcessedErrors());
        }
    }

    public function testValidateRequiredWithValidChainFalse()
    {
        $rules  = [
            'alpha' => 'required-with:beta',
            'beta'  => 'required-with:gamma',
            'gamma' => 'required-with:alpha',
        ];
        $values = ['alpha' => null, 'beta' => null, 'gamma' => null];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithValidChainTrueDots()
    {
        $rules  = [
            '*.alpha' => 'required-with:*.beta',
            '*.beta'  => 'required-with:*.gamma',
            '*.gamma' => 'required-with:*.alpha',
        ];
        $values = [
            ['alpha' => 'one', 'beta' => 'two', 'gamma' => 'three'],
            ['alpha' => 'one', 'beta' => 'two', 'gamma' => 'three'],
            ['alpha' => 'one', 'beta' => 'two', 'gamma' => 'three'],
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateRequiredWithInvalidChainTrueDots()
    {
        $rules  = [
            '*.alpha' => 'required-with:*.beta',
            '*.beta'  => 'required-with:*.gamma',
            '*.gamma' => 'required-with:*.alpha',
        ];
        $values = [
            ['alpha' => null, 'beta' => 'two', 'gamma' => 'three'],
            ['alpha' => 'one', 'beta' => null, 'gamma' => 'three'],
            ['alpha' => 'one', 'beta' => 'two', 'gamma' => null],
        ];
        $errors = ['errors' => [
            '0.alpha' => ['required-with' => '0 alpha is required when 0 beta is present'],
            '1.beta'  => ['required-with' => '1 beta is required when 1 gamma is present'],
            '2.gamma' => ['required-with' => '2 gamma is required when 2 alpha is present'],
        ]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testValidateRequiredWithValidChainFalseDots()
    {
        $rules  = [
            '*.alpha' => 'required-with:*.beta',
            '*.beta'  => 'required-with:*.gamma',
            '*.gamma' => 'required-with:*.alpha',
        ];
        $values = [
            ['alpha' => null, 'beta' => null, 'gamma' => null],
            ['alpha' => null, 'beta' => null, 'gamma' => null],
            ['alpha' => null, 'beta' => null, 'gamma' => null],
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
}