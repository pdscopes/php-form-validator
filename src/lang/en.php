<?php

return function (\MadeSimple\Validator\Validator $validator) {
    $validator
        ->setRuleMessage('present', ':attribute must be present')
        ->setRuleMessage('required', ':attribute !is|are required')
        ->setRuleMessage('required-if', ':attribute !is|are required if :field equals %value')
        ->setRuleMessage('required-with', ':attribute !is|are required when :field is present')
        ->setRuleMessage('required-with-all', ':attribute !is|are required')
        ->setRuleMessage('required-with-any', ':attribute !is|are required')
        ->setRuleMessage('required-without', ':attribute !is|are required when :field is not present')

        ->setRuleMessage('equals', ':attribute must equal :field')
        ->setRuleMessage('not-equals', ':attribute must not equal :field')
        ->setRuleMessage('identical', ':attribute must be identical to :field')
        ->setRuleMessage('not-identical', ':attribute must not be identical to :field')

        ->setRuleMessage('in', ':attribute must be one of: %values')
        ->setRuleMessage('not-in', ':attribute must not be one of: %values')
        ->setRuleMessage('contains', ':attribute must contain: %values')
        ->setRuleMessage('contains-only', ':attribute must only contain: %values')
        ->setRuleMessage('min-arr-count', ':attribute must contain at least :min item(s)')
        ->setRuleMessage('max-arr-count', ':attribute must contain at most :max item(s)')

        ->setRuleMessage('min', ':attribute must be at least :min')
        ->setRuleMessage('max', ':attribute must be at most :max')
        ->setRuleMessage('greater-than', ':attribute must be greater than :field')
        ->setRuleMessage('less-than', ':attribute must be less than :field')

        ->setRuleMessage('alpha', ':attribute must only contain alpha characters')
        ->setRuleMessage('alpha-numeric', ':attribute must only contain alpha-numeric characters')
        ->setRuleMessage('min-str-len', ':attribute must be at least :min character(s) long')
        ->setRuleMessage('max-str-len', ':attribute must be at most :max character(s) long')
        ->setRuleMessage('str-len', ':attribute must be exactly %value long')
        ->setRuleMessage('human-name', ':attribute must be a valid name')

        ->setRuleMessage('is', ':attribute must be a type of :type')

        ->setRuleMessage('email', ':attribute must be an email address')
        ->setRuleMessage('date', ':attribute must be a date in the format: :format')
        ->setRuleMessage('url', ':attribute must be a valid URL')
        ->setRuleMessage('uuid', ':attribute must be a valid UUID')

        ->setRuleMessage('card-number', ':attribute must be a valid card number');
};