<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateMinArrCountTest extends TestCase
{
    use ValidateTrait;

    public function testMinArrCountValid()
    {
        $rules  = ['field' => 'min-arr-count:2'];
        $values = ['field' => ['one', 'two']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testMinArrCountInvalid()
    {
        $rules  = ['field' => 'min-arr-count:2'];
        $values = ['field' => ['one']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}