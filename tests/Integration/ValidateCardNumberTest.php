<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateCardNumberTest extends TestCase
{
    use ValidateTrait;

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
}