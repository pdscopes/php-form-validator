<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateContainsOnlyTest extends TestCase
{
    use ValidateTrait;

    public function testValidateContainsOnlyValid()
    {
        $rules  = ['field' => 'contains-only:alpha,beta,gamma'];
        $values = ['field' => ['alpha','gamma']];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateContainsOnlyInvalid()
    {
        $rules  = ['field' => 'contains-only:alpha,beta,gamma'];
        $values = ['field' => ['alpha', 'gamma', 'omega']];
        $this->validator->validate($values, $rules);

        $this->assertTrue($this->validator->hasErrors());
    }
}