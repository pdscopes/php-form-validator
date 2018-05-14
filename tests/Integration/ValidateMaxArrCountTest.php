<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateMaxArrCountTest extends TestCase
{
    use ValidateTrait;

    public function testMaxArrCountValid()
    {
        $rules  = ['field' => 'max-arr-count:2'];
        $values = ['field' => ['one', 'two']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }

    public function testMaxArrCountInvalid()
    {
        $rules  = ['field' => 'max-arr-count:2'];
        $values = ['field' => ['one', 'two', 'three']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}