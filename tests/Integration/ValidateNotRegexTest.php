<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateNotRegexTest extends TestCase
{
    use ValidateTrait;

    public function testValidateNotRegexValid()
    {
        $rules  = ['field' => ['not-regex:/[ab]{1,2}\d+\s$/i']];
        $values = ['field' => 'ba213'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateNotRegexInvalid()
    {
        $rules  = ['field' => ['not-regex:/[ab]{1,2}\d+\s$/i']];
        $values = ['field' => 'ab1 '];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}