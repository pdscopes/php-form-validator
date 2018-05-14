<?php

namespace MadeSimple\Validator\Test\Integration;

use PHPUnit\Framework\TestCase;

class ValidateNotIdenticalTest extends TestCase
{
    use ValidateTrait;

    public function testValidateNotIdenticalValid()
    {
        $rules  = ['choice2' => 'not-identical:choice1'];
        $values = ['choice1' => 'apple', 'choice2' => 'orang'];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
    public function testValidateNotIdenticalValidDots()
    {
        $rules  = ['field.*.choice2' => 'not-identical:field.*.choice1'];
        $values = [
            'field' => [
                ['choice1' => 'apple', 'choice2' => 'orange'],
                ['choice1' => 1, 'choice2' => '1']
            ]
        ];
        $this->validator->validate($values, $rules);

        $this->assertFalse($this->validator->hasErrors());
    }
}