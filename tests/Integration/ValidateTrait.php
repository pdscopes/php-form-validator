<?php

namespace MadeSimple\Validator\Test\Integration;

use MadeSimple\Validator\Validator;

trait ValidateTrait
{
    /**
     * @var \MadeSimple\Validator\Validator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        if ($this->validator === null) {
            $this->validator = new Validator();
        } else {
            $this->validator->reset();
        }
    }
}