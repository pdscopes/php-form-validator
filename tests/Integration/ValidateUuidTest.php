<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateUuidTest extends TestCase
{
    use ValidateTrait;

    public function testValidateUuidValid()
    {
        $rules  = ['field' => 'uuid'];
        $values = ['field' => '6278cf6f-f69d-4784-a1b7-0d5a53ff2b9f'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateUuidInvalid()
    {
        $rules  = ['field' => 'uuid'];
        $values = ['field' => '123'];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}