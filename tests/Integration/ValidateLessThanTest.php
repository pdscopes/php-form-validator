<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateLessThanTest extends TestCase
{
    use ValidateTrait;

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
}