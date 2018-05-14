<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidatorHumanNameTest extends TestCase
{
    use ValidateTrait;

    public function testValidateHumanNameValid()
    {
        $rules  = ['field' => 'human-name'];
        $values = ['field' => 'John Doe'];
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