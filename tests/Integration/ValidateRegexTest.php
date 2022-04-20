<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateRegexTest extends TestCase
{
    use ValidateTrait;

    public function testValidateRegexValid()
    {
        $rules  = ['field' => ['regex:/[ab]{1,2}\d+\s$/i']];
        $values = ['field' => 'ba213 '];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());

        $rules  = ['field' => ['regex:/^\S\w\d+\s$/i']];
        $values = ['field' => '1g111 '];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateRegexInvalid()
    {
        $rules  = ['field' => ['regex:/[ab]{1,2}\d+\s$/i']];
        $values = ['field' => 'aaa'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}