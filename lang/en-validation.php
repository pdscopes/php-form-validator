<?php

return [
    'present'       => ':attribute must be present',
    'required'      => ':attribute is required',
    'required-if'   => ':attribute is required if :field equals :value',
    'required-with' => ':attribute is required when :field is present',

    'equals'        => ':attribute must equal :field',
    'not-equals'    => ':attribute must not equal :field',
    'identical'     => ':attribute must be identical to :field',
    'not-identical' => ':attribute must not be identical to :field',

    'in'            => ':attribute must be: :values',
    'not-in'        => ':attribute must not be: :values',
    'contains-only' => ':attribute must only contain: :values',
    'min-arr-count' => ':attribute must contain at least :min item(s)',
    'max-arr-count' => ':attribute must contain at most :max item(s)',

    'min'           => ':attribute must be at least :min',
    'max'           => ':attribute must be at most :max',
    'greater-than'  => ':attribute must be greater than :field',
    'less-than'     => ':attribute must be less than :field',

    'alpha'         => ':attribute must only contain alpha characters',
    'alpha-numeric' => ':attribute must only contain alpha-numeric characters',
    'min-str-len'   => ':attribute must be at least :min character(s) long',
    'max-str-len'   => ':attribute must be at most :max character(s) long',
    'str-len'       => ':attribute must be exactly :value long',
    'human-name'    => ':attribute must be a valid name',

    'is'            => ':attribute must be a type of :type',

    'email'         => ':attribute must be an email address',
    'date'          => ':attribute must be a date in the format: :format',
    'url'           => ':attribute must be a valid URL',
    'uuid'          => ':attribute must be a valid UUID',

    'card-number'   => ':attribute must be a valid card number',
];