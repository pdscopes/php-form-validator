<?php

namespace MadeSimple\Validator;

use MadeSimple\Arrays\ArrDots;

class Validate
{
    /**
     * @param \MadeSimple\Validator\Validator $validator
     */
    public static function addRuleSet(Validator $validator)
    {
        $validator
            ->addRule('present', [static::class, 'present'])
            ->addRule('required', [static::class, 'required'])
            ->addRule('required-if', [static::class, 'requiredIf'])
            ->addRule('required-with', [static::class, 'requiredWith'])

            ->addRule('equals', [static::class, 'equals'])
            ->addRule('not-equals', [static::class, 'notEquals'])
            ->addRule('identical', [static::class, 'identical'])
            ->addRule('not-identical', [static::class, 'notIdentical'])

            ->addRule('in', [static::class, 'in'])
            ->addRule('not-in', [static::class, 'notIn'])
            ->addRule('contains-only', [static::class, 'containsOnly'])
            ->addRule('min-arr-count', [static::class, 'minArrCount'])
            ->addRule('max-arr-count', [static::class, 'maxArrCount'])

            ->addRule('min', [static::class, 'min'])
            ->addRule('max', [static::class, 'max'])
            ->addRule('greater-than', [static::class, 'greaterThan'])
            ->addRule('less-than', [static::class, 'lessThan'])

            ->addRule('alpha', [static::class, 'alpha'])
            ->addRule('alpha-numeric', [static::class, 'alphaNumeric'])
            ->addRule('min-str-len', [static::class, 'minStrLen'])
            ->addRule('max-str-len', [static::class, 'maxStrLen'])
            ->addRule('str-len', [static::class, 'strLen'])
            ->addRule('human-name', [static::class, 'humanName'])

            ->addRule('is', [static::class, 'is'])

            ->addRule('email', [static::class, 'email'])
            ->addRule('date', [static::class, 'date'])
            ->addRule('url', [static::class, 'url'])
            ->addRule('uuid', [static::class, 'uuid'])

            ->addRule('card-number', [static::class, 'cardNumber']);
    }


    /**
     * present
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function present(Validator $validator, $data, $pattern, $rule)
    {
        if (ArrDots::has($data, $pattern, $validator::WILD)) {
            return;
        }

        $validator->addError($pattern, $rule);
    }

    /**
     * required
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function required(Validator $validator, $data, $pattern, $rule)
    {
        // Check pattern is present
        if (!ArrDots::has($data, $pattern, $validator::WILD)) {
            $validator->addError($pattern, $rule);
        }

        // Check value is not null
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null !== $value) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }

    /**
     * required-if:another-field(,value)+
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function requiredIf(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $field  = $parameters[0];
        $values = array_slice($parameters, 1);
        $isWild  = strpos($field, $validator::WILD) !== false;
        $overlap = Str::overlapLeft($field, $pattern);

        // If pattern is not present
        if (!ArrDots::has($data, $pattern, $validator::WILD)) {
            foreach (Validator::getValues($data, $field) as $fieldAttribute => $fieldValue) {
                if (null === $fieldValue || !in_array($fieldValue, $values)) {
                    continue;
                }

                $attribute = $isWild ? Str::overlapLeftMerge($overlap, $fieldAttribute, $pattern) : $pattern;
                $validator->addError($attribute, $rule, [':field' => $fieldAttribute, '%value' => implode(',', $values)]);
            }
            return;
        }

        // Check value is not null
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlapLeftMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue === null || !in_array($fieldValue, $values)) {
                continue;
            }

            if (null !== $value) {
                continue;
            }

            $validator->addError($attribute, $rule, [':field' => $fieldAttribute, '%value' => implode(',', $values)]);
        }
    }

    /**
     * required-with:another-field
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function requiredWith(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, $validator::WILD) !== false;
        $overlap = Str::overlapLeft($field, $pattern);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern ('.$pattern.') to field ('.$field.')');
        }

        // If the required with field exists and the pattern field does not
        if (ArrDots::has($data, $field, $validator::WILD) && !ArrDots::has($data, $pattern, $validator::WILD)) {
            $validator->addError($pattern, $rule, [':field' => $field]);
        }

        // Check value is not null
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlapLeftMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue === null) {
                continue;
            }
            if ($fieldValue !== null && $value !== null) {
                continue;
            }

            $validator->addError($attribute, $rule, [':field' => $fieldAttribute]);
        }
    }


    /**
     * equals:another-field
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function equals(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, $validator::WILD) !== false;
        $overlap = Str::overlapLeft($field, $pattern);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern ('.$pattern.') to field ('.$field.')');
        }

        // Check values are equal
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlapLeftMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue == $value) {
                continue;
            }

            $validator->addError($attribute, $rule, [':field' => $fieldAttribute]);
        }
    }

    /**
     * not-equals:another-field
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function notEquals(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, $validator::WILD) !== false;
        $overlap = Str::overlapLeft($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlapLeftMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue != $value) {
                continue;
            }

            $validator->addError($attribute, $rule, [':field' => $fieldAttribute]);
        }
    }

    /**
     * identical:another-field
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function identical(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, $validator::WILD) !== false;
        $overlap = Str::overlapLeft($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlapLeftMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue === $value) {
                continue;
            }

            $validator->addError($attribute, $rule, [':field' => $fieldAttribute]);
        }
    }

    /**
     * not-identical:another-field
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function notIdentical(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, $validator::WILD) !== false;
        $overlap = Str::overlapLeft($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlapLeftMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue !== $value) {
                continue;
            }

            $validator->addError($attribute, $rule, [':field' => $fieldAttribute]);
        }
    }


    /**
     * in:<value>(,<value>)*
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function in(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value) {
                continue;
            }
            if (in_array($value, $parameters)) {
                continue;
            }

            $validator->addError($attribute, $rule, ['%values' => implode(', ', $parameters)]);
        }
    }

    /**
     * not-in:<value>(,<value>)*
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function notIn(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value) {
                continue;
            }
            if (!in_array($value, $parameters)) {
                continue;
            }

            $validator->addError($attribute, $rule, ['%values' => implode(', ', $parameters)]);
        }
    }

    /**
     * contains-only:<value>(,<value>)*
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function containsOnly(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (count($value) == count(array_intersect($value, $parameters))) {
                continue;
            }

            $validator->addError($attribute, $rule, [':values' => implode(', ', $parameters)]);
        }
    }

    /**
     * min-arr-count:<minimum_value>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function minArrCount(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $min = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value) {
                continue;
            }

            if (count($value) >= $min) {
                break;
            }

            $validator->addError($attribute, $rule, [':min' => $min]);
        }
    }

    /**
     * max-arr-count:<minimum_value>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function maxArrCount(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $max = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value) {
                continue;
            }

            if (count($value) <= $max) {
                break;
            }

            $validator->addError($attribute, $rule, [':max' => $max]);
        }
    }


    /**
     * min:<minimum-value>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function min(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $min = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || ($value != '0' && empty($value))) {
                continue;
            }

            if ($value >= $min) {
                break;
            }

            $validator->addError($attribute, $rule, [':min' => $min]);
        }
    }

    /**
     * max:<minimum_value>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function max(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $max = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || ($value != '0' && empty($value))) {
                continue;
            }

            if ($value <= $max) {
                break;
            }

            $validator->addError($attribute, $rule, [':max' => $max]);
        }
    }

    /**
     * greater-than:<another_field>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function greaterThan(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $lowerBound = Validator::getValue($data, $parameters[0]);
        if (null === $lowerBound) {
            return;
        }
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if ($value > $lowerBound) {
                continue;
            }
            $validator->addError($attribute, $rule, [':value' => $value]);
        }
    }

    /**
     * less-than:<another_field>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function lessThan(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $upperBound = Validator::getValue($data, $parameters[0]);
        if (null === $upperBound) {
            return;
        }
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if ($value < $upperBound) {
                continue;
            }

            $validator->addError($attribute, $rule, [':value' => $value]);
        }
    }


    /**
     * alpha
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function alpha(Validator $validator, $data, $pattern, $rule)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }

    /**
     * alpha-numeric
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function alphaNumeric(Validator $validator, $data, $pattern, $rule)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }

    /**
     * min-str-len:<minimum_value>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function minStrLen(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $min = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }

            if (strlen($value) >= $min) {
                break;
            }

            $validator->addError($attribute, $rule, [':min' => $min]);
        }
    }

    /**
     * max-str-len:<minimum_value>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function maxStrLen(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $max = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }

            if (strlen($value) <= $max) {
                break;
            }

            $validator->addError($attribute, $rule, [':max' => $max]);
        }
    }

    /**
     * str-len:<exact-length>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function strLen(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $length = $parameters[0];

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (strlen($value) === (int) $length) {
                continue;
            }

            $validator->addError($attribute, $rule, [':length' => $length]);
        }
    }

    /**
     * human-name
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function humanName(Validator $validator, $data, $pattern, $rule)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ \'-])+$/i', $value) === 1) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }


    /**
     * is:<type>
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function is(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $is_a_func = sprintf('is_%s', $parameters[0]);
        if (!function_exists($is_a_func)) {
            return;
        }

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            // As "is:<type>" is validating value type only ignore null
            if (null === $value) {
                continue;
            }
            if (call_user_func($is_a_func, $value)) {
                continue;
            }

            $validator->addError($attribute, $rule, [':type' => $parameters[0]]);
        }
    }


    /**
     * email
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function email(Validator $validator, $data, $pattern, $rule)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (false !== filter_var($value, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }

    /**
     * date:(format)?
     *
     * @link http://php.net/manual/en/datetime.createfromformat.php
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public static function date(Validator $validator, $data, $pattern, $rule, $parameters)
    {
        $format = !empty($parameters[0]) ? $parameters[0] : 'Y-m-d';
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            $d = \DateTime::createFromFormat($format, $value);
            if ($d && $d->format($format) == $value) {
                continue;
            }

            $validator->addError($attribute, $rule, [':format' => $format]);
        }
    }

    /**
     * url
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function url(Validator $validator, $data, $pattern, $rule)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (false !== filter_var($value, FILTER_VALIDATE_URL)) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }

    /**
     * uuid
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function uuid(Validator $validator, $data, $pattern, $rule)
    {
        $uuidPattern = '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/';

        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (1 === preg_match($uuidPattern, $value)) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }


    /**
     * card-number
     *
     * @see http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php
     *
     * @param \MadeSimple\Validator\Validator $validator
     * @param array $data
     * @param string $pattern
     * @param string $rule
     */
    public static function cardNumber(Validator $validator, $data, $pattern, $rule)
    {
        foreach (Validator::getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }

            // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
            $number = preg_replace('/\D/', '', $value);

            // Set the string length and parity
            $numberLength = strlen($number);
            $parity       = $numberLength % 2;

            // Loop through each digit and do the maths
            $total = 0;
            for ($i = 0; $i < $numberLength; $i++) {
                $digit = $number[$i];
                // Multiply alternate digits by two
                if ($i % 2 == $parity) {
                    $digit *= 2;
                    // If the sum is two digits, add them together (in effect)
                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }
                // Total up the digits
                $total += $digit;
            }

            // If the total mod 10 equals 0, the number is valid
            if ($total % 10 == 0) {
                continue;
            }

            $validator->addError($attribute, $rule);
        }
    }
}