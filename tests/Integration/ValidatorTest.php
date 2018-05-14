<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    use ValidateTrait;

    public function testSingularAttribute()
    {
        $rules  = [
            'field1' => 'required|required-if:field0,yes|required-with:field0',
        ];
        $values = [
            'field0' => 'yes',
            'field1' => null,
        ];
        $errors = ['errors' => [
            'field1' => [
                'required'      => 'Field1 is required',
                'required-if'   => 'Field1 is required if Field0 equals yes',
                'required-with' => 'Field1 is required when Field0 is present',
            ]
        ]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    public function testPluralAttribute()
    {
        $rules  = [
            'fields.*' => 'required|required-if:field,yes|required-with:field',
        ];
        $values = [
            'field'  => 'yes',
            'fields' => null,
        ];
        $errors = ['errors' => [
            'fields.*' => [
                'required'      => 'Fields are required',
                'required-if'   => 'Fields are required if Field equals yes',
                'required-with' => 'Fields are required when Field is present',
            ]
        ]];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }

    /**
     * @param string $attribute
     * @param string $message
     * @dataProvider customAttributeMessageProvider
     */
    public function testCustomAttributeMessage($attribute, $message)
    {
        $this->validator->setAttributeMessage($attribute, $message);

        $rules  = [$attribute => 'required'];
        $values = [$attribute => null];
        $errors = ['errors' => [
            $attribute => ['required' => $message],
        ]];

        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals($errors, $this->validator->getProcessedErrors());
    }
    public function customAttributeMessageProvider()
    {
        return [
            ['foo', 'This is a custom message'],
        ];
    }
}