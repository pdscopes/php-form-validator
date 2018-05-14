<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateDateTest extends TestCase
{
    use ValidateTrait;

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
}