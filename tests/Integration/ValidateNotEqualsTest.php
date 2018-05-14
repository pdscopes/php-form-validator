<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateNotEqualsTest extends TestCase
{
    use ValidateTrait;

    public function testValidateNotEqualsValid()
    {
        $rules  = ['choice2' => 'not-equals:choice1'];
        $values = ['choice1' => 'apple', 'choice2' => 'orange'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateNotEqualsValidDots()
    {
        $rules  = ['field.*.choice2' => 'not-equals:field.*.choice1'];
        $values = [
            'field' => [
                ['choice1' => 'apple', 'choice2' => 'orange'],
                ['choice1' => 1, 'choice2' => '2']
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateNotEqualsInvalid()
    {
        $rules  = ['choice2' => 'not-equals:choice1'];
        $values = ['choice1' => 'apple', 'choice2' => 'apple'];
        $errors = ['errors' => ['choice2' => ['not-equals' => 'Choice2 must not equal Choice1']]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    public function testValidateNotEqualsInvalidDots()
    {
        $rules  = ['field.*.choice2' => 'not-equals:field.*.choice1'];
        $values = [
            'field' => [
                ['choice1' => 'apple', 'choice2' => 'apple'],
                ['choice1' => 1, 'choice2' => '1']
            ]
        ];
        $errors = ['errors' => [
            'field.0.choice2' => ['not-equals' => 'Field 0 choice2 must not equal Field 0 choice1'],
            'field.1.choice2' => ['not-equals' => 'Field 1 choice2 must not equal Field 1 choice1'],
        ]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
}