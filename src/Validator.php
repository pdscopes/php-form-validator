<?php

namespace MadeSimple\Validator;

use MadeSimple\Arrays\Arr;
use MadeSimple\Arrays\ArrDots;

/**
 * Class Validator
 *
 * @package MadeSimple\Validator
 * @author  Peter Scopes <peter.scopes@gmail.com>
 */
class Validator
{
    /**
     * @var array Associative array of rule name to callable
     */
    protected $validators = [];

    /**
     * @var array Associative array of rule name to message
     */
    protected $messages;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Validator constructor.
     *
     * @param string $lang
     * @param string $langDir
     */
    public function __construct($lang = 'en', $langDir = __DIR__ . '/../lang/')
    {
        $this->setLanguage($lang, $langDir)->reset();
    }

    /**
     * Set the validator language.
     *
     * @param string $lang
     * @param string $langDir
     *
     * @return Validator
     */
    public function setLanguage($lang = 'en', $langDir = __DIR__ . '/../lang/')
    {
        $langFile = realpath($langDir . $lang . '-validation.php');
        if (!file_exists($langFile)) {
            throw new \InvalidArgumentException('No such file: ' . $langFile);
        }

        $this->messages = require $langFile;

        return $this;
    }

    /**
     * Resets the validator to its initial state.
     *
     * @return Validator
     */
    public function reset()
    {
        $this->errors   = [];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Return a processed array of errors.
     *
     * @return array
     */
    public function getProcessedErrors()
    {
        $errors = [];

        foreach ($this->errors as $error) {
            $errors[$error['attribute']][$error['rule']] = str_replace(
                $error['search'],
                $error['replace'],
                null !== $error['message'] ? $error['message'] : ArrDots::get($this->messages, $error['type'])
            );
        }

        return ['errors' => $errors];
    }

    /**
     * @param array|null|object $values
     * @param array             $ruleSet
     *
     * @return void
     */
    public function validate($values, array $ruleSet)
    {
        // If there are no rules, there is nothing to validate
        if(empty($ruleSet)) {
            return;
        }

        // For each pattern and its rules
        foreach ($ruleSet as $pattern => $rules) {
            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }
            foreach ($rules as $rule) {
                list($rule, $parameters) = array_pad(explode(':', $rule, 2), 2, '');
                $parameters = array_map('trim', explode(',', $parameters));

                if (Arr::exists($this->validators, $rule)) {
                    call_user_func($this->validators[$rule], $values, $pattern, $rule, $parameters);
                }
                else if (method_exists($this, 'validate'.Str::dashesToCamel($rule))) {
                    call_user_func([$this, 'validate'.Str::dashesToCamel($rule)], $values, $pattern, $rule, $parameters);
                }
            }
        }
    }

    /**
     * @param string      $attribute
     * @param string      $rule
     * @param array       $replacements
     * @param null|string $type
     * @param null|string $message
     */
    protected function addError($attribute, $rule, $replacements = [], $type = null, $message = null)
    {
        $replacements = array_merge([':attribute' => Str::prettyAttribute($attribute)], $replacements);

        $this->errors[] = [
            'attribute' => $attribute,
            'rule'      => $rule,
            'search'    => array_keys($replacements),
            'replace'   => array_values($replacements),
            'type'      => null === $type ? $rule : $type,
            'message'   => $message
        ];
    }

    /**
     * @param array  $array
     * @param string $pattern
     *
     * @return \Generator
     */
    protected function getValues(&$array, $pattern)
    {
        foreach (ArrDots::search($array, $pattern, '*') as $attribute => $value) {
            yield $attribute => $value;
        }
    }

    /**
     * @param array $array
     * @param string $pattern
     *
     * @return mixed|null First matching value or null
     */
    protected function getValue(&$array, $pattern)
    {
        $imploded = ArrDots::implode($array);
        $pattern  = sprintf('/^%s$/', str_replace('*', '[0-9]+', $pattern));

        foreach ($imploded as $attribute => $value) {
            if (preg_match($pattern, $attribute) == 0) {
                continue;
            }

            return $value;
        }

        return null;
    }



    /**
     * present
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validatePresent($data, $pattern, $rule)
    {
        if (ArrDots::has($data, $pattern, '*')) {
            return;
        }

        $this->addError($pattern, $rule);
    }

    /**
     * required
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateRequired($data, $pattern, $rule)
    {
        // Check pattern is present
        if (!ArrDots::has($data, $pattern, '*')) {
            $this->addError($pattern, $rule);
        }

        // Check value is not null
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null !== $value) {
                continue;
            }

            $this->addError($attribute, $rule);
        }
    }

    /**
     * required-if:another-field(,value)+
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateRequiredIf($data, $pattern, $rule, $parameters)
    {
        $field  = $parameters[0];
        $values = array_slice($parameters, 1);

        $required = false;
        foreach ($this->getValues($data, $field) as $attribute => $value) {
            $required = $required || in_array($value, $values);
        }
        if (!$required) {
            return;
        }


        // Check pattern is present
        if (!ArrDots::has($data, $pattern, '*')) {
            $this->addError($pattern, $rule, [':field' => Str::prettyAttribute($field), ':value' => implode(',', $values)]);
        }

        // Check value is not null
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null !== $value) {
                continue;
            }

            $this->addError($attribute, $rule, [':field' => Str::prettyAttribute($field), ':value' => implode(',', $values)]);
        }
    }

    /**
     * required-with:another-field
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateRequiredWith($data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, '*') !== false;
        $overlap = Str::overlapl($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check value is not null
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlaplMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue === null) {
                continue;
            }
            if ($fieldValue !== null && $value !== null) {
                continue;
            }

            $this->addError($attribute, $rule, [':field' => Str::prettyAttribute($fieldAttribute)]);
        }
    }


    /**
     * equals:another-field
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateEquals($data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, '*') !== false;
        $overlap = Str::overlapl($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlaplMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue == $value) {
                continue;
            }

            $this->addError($attribute, $rule, [':field' => Str::prettyAttribute($fieldAttribute)]);
        }
    }

    /**
     * not-equals:another-field
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateNotEquals($data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, '*') !== false;
        $overlap = Str::overlapl($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlaplMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue != $value) {
                continue;
            }

            $this->addError($attribute, $rule, [':field' => Str::prettyAttribute($fieldAttribute)]);
        }
    }

    /**
     * identical:another-field
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateIdentical($data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, '*') !== false;
        $overlap = Str::overlapl($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlaplMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue === $value) {
                continue;
            }

            $this->addError($attribute, $rule, [':field' => Str::prettyAttribute($fieldAttribute)]);
        }
    }

    /**
     * not-identical:another-field
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateNotIdentical($data, $pattern, $rule, $parameters)
    {
        $field   = $parameters[0];
        $isWild  = strpos($field, '*') !== false;
        $overlap = Str::overlapl($pattern, $field);

        // Check that the pattern and field can be compared
        if ($isWild && $overlap === false) {
            throw new \InvalidArgumentException('Cannot match pattern to field');
        }

        // Check values are equal
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            $fieldAttribute = $isWild ? Str::overlaplMerge($overlap, $attribute, $field) : $field;
            $fieldValue     = ArrDots::get($data, $fieldAttribute);

            if ($fieldValue !== $value) {
                continue;
            }

            $this->addError($attribute, $rule, [':field' => Str::prettyAttribute($fieldAttribute)]);
        }
    }


    /**
     * in:<value>(,<value>)*
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateIn($data, $pattern, $rule, $parameters)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value) {
                continue;
            }
            if (in_array($value, $parameters)) {
                continue;
            }

            $this->addError($attribute, $rule, [':values' => implode(', ', $parameters)]);
        }
    }

    /**
     * not-in:<value>(,<value>)*
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateNotIn($data, $pattern, $rule, $parameters)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value) {
                continue;
            }
            if (!in_array($value, $parameters)) {
                continue;
            }

            $this->addError($attribute, $rule, [':values' => implode(', ', $parameters)]);
        }
    }

    /**
     * contains-only:<value>(,<value>)*
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateContainsOnly($data, $pattern, $rule, $parameters)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (count($value) == count(array_intersect($value, $parameters))) {
                continue;
            }

            $this->addError($attribute, $rule, [':values' => implode(', ', $parameters)]);
        }
    }

    /**
     * min-arr-count:<minimum_value>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateMinArrCount($data, $pattern, $rule, $parameters)
    {
        $min = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute2 => $value) {
            if (null === $value) {
                continue;
            }

            if (count($value) >= $min) {
                break;
            }

            $this->addError($attribute2, $rule, [':min' => $min], $rule);
        }
    }

    /**
     * max-arr-count:<minimum_value>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateMaxArrCount($data, $pattern, $rule, $parameters)
    {
        $max = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute2 => $value) {
            if (null === $value) {
                continue;
            }

            if (count($value) <= $max) {
                break;
            }

            $this->addError($attribute2, $rule, [':max' => $max], $rule);
        }
    }


    /**
     * min:<minimum-value>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateMin($data, $pattern, $rule, $parameters)
    {
        $min = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute2 => $value) {
            if (null === $value || ($value != '0' && empty($value))) {
                continue;
            }

            if ($value >= $min) {
                break;
            }

            $this->addError($attribute2, $rule, [':min' => $min], $rule);
        }
    }

    /**
     * max:<minimum_value>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateMax($data, $pattern, $rule, $parameters)
    {
        $max = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute2 => $value) {
            if (null === $value || ($value != '0' && empty($value))) {
                continue;
            }

            if ($value <= $max) {
                break;
            }

            $this->addError($attribute2, $rule, [':max' => $max], $rule);
        }
    }

    /**
     * greater-than:<another_field>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateGreaterThan($data, $pattern, $rule, $parameters)
    {
        $lowerBound = $this->getValue($data, $parameters[0]);
        if (null === $lowerBound) {
            return;
        }
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if ($value > $lowerBound) {
                continue;
            }
            $this->addError($attribute, $rule, [':value' => $value]);
        }
    }

    /**
     * less-than:<another_field>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateLessThan($data, $pattern, $rule, $parameters)
    {
        $upperBound = $this->getValue($data, $parameters[0]);
        if (null === $upperBound) {
            return;
        }
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if ($value < $upperBound) {
                continue;
            }

            $this->addError($attribute, $rule, [':value' => $value]);
        }
    }


    /**
     * alpha
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateAlpha($data, $pattern, $rule)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1) {
                continue;
            }

            $this->addError($attribute, $rule);
        }
    }

    /**
     * alpha-numeric
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateAlphaNumeric($data, $pattern, $rule)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1) {
                continue;
            }

            $this->addError($attribute, $rule);
        }
    }

    /**
     * min-str-len:<minimum_value>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateMinStrLen($data, $pattern, $rule, $parameters)
    {
        $min = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute2 => $value) {
            if (null === $value || empty($value)) {
                continue;
            }

            if (strlen($value) >= $min) {
                break;
            }

            $this->addError($attribute2, $rule, [':min' => $min], $rule);
        }
    }

    /**
     * max-str-len:<minimum_value>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateMaxStrLen($data, $pattern, $rule, $parameters)
    {
        $max = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute2 => $value) {
            if (null === $value || empty($value)) {
                continue;
            }

            if (strlen($value) <= $max) {
                break;
            }

            $this->addError($attribute2, $rule, [':max' => $max], $rule);
        }
    }

    /**
     * str-len:<exact-length>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateStrLen($data, $pattern, $rule, $parameters)
    {
        $length = $parameters[0];

        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (strlen($value) === (int) $length) {
                continue;
            }

            $this->addError($attribute, $rule, [':length' => $length], 'length');
        }
    }

    /**
     * human-name
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateHumanName($data, $pattern, $rule)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ \'-])+$/i', $value) === 1) {
                continue;
            }

            $this->addError($attribute, $rule);
        }
    }


    /**
     * is:<type>
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateIs($data, $pattern, $rule, $parameters)
    {
        $is_a_func = sprintf('is_%s', $parameters[0]);
        if (!function_exists($is_a_func)) {
            return;
        }

        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (call_user_func($is_a_func, $value)) {
                continue;
            }

            $this->addError($attribute, $rule, [':type' => $parameters[0]], 'is');
        }
    }


    /**
     * email
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateEmail($data, $pattern, $rule)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (false !== filter_var($value, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $this->addError($attribute, $rule);
        }
    }

    /**
     * date:(format)?
     *
     * @link http://php.net/manual/en/datetime.createfromformat.php
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     * @param array  $parameters
     */
    public function validateDate($data, $pattern, $rule, $parameters)
    {
        $format = !empty($parameters[0]) ? $parameters[0] : 'Y-m-d';
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            $d = \DateTime::createFromFormat($format, $value);
            if ($d && $d->format($format) == $value) {
                continue;
            }

            $this->addError($attribute, $rule, [':format' => $format]);
        }
    }

    /**
     * url
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateUrl($data, $pattern, $rule)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            if (false !== filter_var($value, FILTER_VALIDATE_URL)) {
                continue;
            }

            $this->addError($attribute, $rule);
        }
    }


    /**
     * card-number
     *
     * @see http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php
     *
     * @param array  $data
     * @param string $pattern
     * @param string $rule
     */
    public function validateCardNumber($data, $pattern, $rule)
    {
        foreach ($this->getValues($data, $pattern) as $attribute => $value) {
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

            $this->addError($attribute, $rule);
        }
    }
}