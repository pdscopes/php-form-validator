# php-form-validator
[![PHPUnit](https://github.com/pdscopes/php-form-validator/actions/workflows/phpunit.yml/badge.svg)](https://github.com/pdscopes/php-form-validator/actions/workflows/phpunit.yml)

Simple, extendable form validator for multi-dimensional forms

## Installation
PHP Form Validator is available on [Packagist](https://packagist.org/packages/madesimple/php-form-validator) (using semantic versioning), and installation via [Composer](https://getcomposer.org) is the recommended way to install.
Just add this line to your `composer.json` file:

```
"madesimple/php-form-validator": "^2.8"
```

or run:

```shell
composer require madesimple/php-form-validator
```

## Validation Rules
Validation rules are an associative array of dot notation field names
in the input array to a pipe separated string of rules. The asterisk, `*`,
can be use in the dot notation as a wildcard. For example, a set of rules
could look like this:
```php
// Rules Set
$rulesSet = [
    'username'         => 'required|alpha-numeric|max-str-len:50',
    'firstName'        => 'required|human-name|required-with:lastName|max-str-len:255',
    'lastName'         => 'required|human-name|required-with:firstName|max-str-len:255',
    'address.line1'    => 'present',
    'address.postCode' => 'present',
    'likes.*.item'     => 'required-with:likes.*.rating|max-str-len:50'
    'likes.*.stars'    => 'required-with:likes.*.item|is:int|min:1|max:5'
];

// Valid Input
$input = [
    'username' => 'jbloggs',
    'firstName' => 'Joe',
    'lastName => 'Bloggs',
    'address' => [
        'line1' => '123 Fake St',
        'postCode' => 'AB12 3CD',
    ]
    'likes' => [
        [
            'item'  => 'php-form-validator',
            'stars' => 5
        ]
    ]
];
```

The following are all the validation rules that this library provides:

| Rule Name                                   | Keyword             | Parameters       | Description                                                                                                    |
|:--------------------------------------------|:--------------------|:-----------------|:---------------------------------------------------------------------------------------------------------------|
| [Present](#present)                         | `present`           |                  | The field must be present but can have any value including `null`.                                             |
| [Required](#required)                       | `required`          |                  | The field must be present and cannot be `null` (can be an empty string).                                       |
| [Required If](#required-if)                 | `required-if`       | `field(,value)+` | The field is required if the specified field(s) and the specified value(s).                                    |
| [Required With](#required-with)             | `required-with`     | `field`          | The field is required if the other field is not `null`.                                                        |
| [Required With All](#required-with-all)     | `required-with-all` | `field(,field)*` | The field is required if all of the other fields are not `null`.                                               |
| [Required With Any](#required-with-any)     | `required-with-any` | `field(,field)*` | The field is required if any of the other fields are not `null`.                                               |
| [Required Without](#required-without)       | `required-without`  | `field`          | The field is required if the other field is `null`.                                                            |
| [Equals](#equals)                           | `equals`            | `field`          | The field's value must equals the other specified field's value.                                               |
| [Not Equals](#not-equals)                   | `not-equals`        | `field`          | The field's value must not equal the other specified field's value.                                            |
| [Identical](#identical)                     | `identical`         | `field`          | The field's value must be identical the other specified field's value.                                         |
| [Not Identical](#not-identical)             | `not-identical`     | `field`          | The field's value must not be identical the other specified field's value.                                     |
| [In](#in)                                   | `in`                | `value(,value)*` | The field must equal one of the specified options.                                                             |
| [Not In](#not-in)                           | `not-in`            | `value(,value)*` | The field must not equal one of the specified options.                                                         |
| [Contains](#contains)                       | `contains`          | `value(,value)*` | The field should be an array and must contain all the specified options (may contain other values not listed). |
| [Contains Only](#contains-only)             | `contains-only`     | `value(,value)*` | The field should be an array and must contain only the specified options.                                      |
| [Minimum Array Count](#min-array-count)     | `min-arr-count`     | `int`            | The field should be an array and must have an `array_count` of at least the specified value.                   |
| [Maximum Array Count](#max-array-count)     | `max-arr-count`     | `int`            | The field should be an array and must have an `array_count` of at most the specified value.                    |
| [Minimum](#min)                             | `min`               | `int`            | The field should be numeric and must be at least the specified value.                                          |
| [Maximum](#max)                             | `max`               | `int`            | The field should be numeric and must be at most the specified value.                                           |
| [Greater Than](#greater-than)               | `greater-than`      | `field`          | The field should be numeric and must have a value greater than the other field.                                |
| [Less Than](#less-than)                     | `less-than`         | `field`          | The field should be numeric and must have a value less than the other field.                                   |
| [Alpha](#alpha)                             | `alpha`             |                  | The field must only contain alphabetic characters.                                                             |
| [Alpha Numeric](#alpha-numeric)             | `alpha-numeric`     |                  | The field must only contain alphabetic and numerical characters.                                               |
| [Minimum String Length](#min-string-length) | `min-str-len`       | `int`            | The field should be string and must have a `strlen` of at least the specified value.                           |
| [Maximum String Length](#max-string-length) | `max-str-len`       | `int`            | The field should be a string and must have a `strlen` of at most the specified value.                          |
| [String Length](#string-length)             | `str-len`           | `int`            | The field should be a string and must have a `strlen` of exactly the specified value.                          |
| [Human Name](#human-name)                   | `human-name`        |                  | The field must be a valid human name.                                                                          |
| [Is: ...](#is-)                             | `is`                | `type`           | The field must be of the specified basic PHP type. There must be a corresponding `is_<type>` method.           |
| [Email](#email)                             | `email`             |                  | The field must be a valid email address                                                                        |
| [Date](#date)                               | `date`              | `(format)?`      | The field must be a valid date in the specified format (defaults to `'Y-m-d'`).                                |
| [URL](#url)                                 | `url`               |                  | The field must be a valid URL.                                                                                 |
| [UUID](#uuid)                               | `uuid`              |                  | The field must be a valid UUID (\universally unique identifier).                                               |
| [Card Number](#card-number)                 | `card-number`       |                  | The field must be a valid card number.                                                                         |
| [Regex](#regex)                             | `regex`             | `regex pattern`  | The field must match the regex pattern.                                                                       |
| [Not Regex](#not-regex)                     | `not-regex`         | `regex pattern`  | The field must not match the regex pattern.                                                                   |

## Adding Extra Rules
Extra rules can be added to the validator to extend its functionality to provide specific rules for your project.
If you believe your rule should be added to the core library please submit a pull request.
To add your extra rule you must call both `addRule` and `setRuleMessage`.

### Simple extra rule
For example, if you wanted to add a rule that would validate that a timezone was valid:

```php
// Add the rule to the validator
$validator = new Validator;
$validator->addRule('timezone', function (Validator $validator, array $data, $pattern, $rule) {
    foreach ($validator->getValues($data, $pattern as $attribute => $value) {
        if (null === $value) {
            continue;
        }
        if (in_array($value, listTimezones())) {
            continue;
        }

        $validator->addError($attribute, $rule);
    }
});
$validator->setRuleMessage('timezone', ':attribute must be an timezone');


// Validate using the new rule
$rules = [
    'timezone' => 'present|timezone',
];
$validator->validate($_POST, $rules);
```

### Extra rule with parameters
For example, if you wanted to add a rule that would validate that an identifier existed in your database:

```php
// Add the rule to the validator
$validator = new Validator;
$validator->addRule('model-exists', function (Validator $validator, array $data, $pattern, $rule, $array $parameters) {
    // Connect to database
    $db = getDbInstance();
    list($model, $property) = $parameters;

    foreach ($validator->getValues($data, $pattern as $attribute => $value) {
        if (null === $value) {
            continue;
        }
        if (doesModelExist($db, $model, $property, $value)) {
            continue;
        }

        $validator->addError($attribute, $rule, [
            ':model' => $model
        ]);
    }
});
$validator->setRuleMessage('model-exists', ':attribute must be an existing :model');


// Validate using the new rule
$rules = [
    'uuid' => 'required|model-exists:user,uuid',
];
$validator->validate($_POST, $rules);
```

Another example, if you wanted to do a complex validation of a sub-array:

```php
// Add the rule to the validator
$validator = new Validator;
$validator->addRule('complex', function (Validator $validator, array $data, $pattern, $rule, $array $parameters) {
    foreach ($validator->getValues($data, $pattern as $attribute => $value) {
        if (null === $value) {
            continue;
        }
        $rules['type'] = 'in:alpha,beta';
        switch ($value['type']) {
            case 'alpha':
                $rules['shared_field'] = 'is:int';
                $rules['alpha_specific_field'] = 'is:int';
                break;

            case 'beta':
                $rules['shared_field'] = 'in:blue,green';
                $rules['beta_specific_field'] = 'is:int';
                break;
        }

        // Apply the type specific rules to this part of the data
        $validator->validate($value, $rules, $attribute);
    }
});
// No need to define a rule message as only sub-rules can generate errors
```


## Validation Rules
### Present
The field must be present but can have any value including `null`.
```php
// Example usage:
$rulesSet = [
    'field' => 'present',
];
```

### Required
The field must be present and cannot be `null` (can be an empty string).
```php
// Example Usage
$rulesSet = [
    'field' => 'required'
];
```

### Required If
The field is required if the specified field(s) and the specified value(s).
```php
// Example Usage
$rulesSet = [
    'field0' => 'required-if:field1:yes',
    'field1' => 'required|in:yes,no'
];
```

### Required With
The field is required if the other field is not `null`.
```php
// Example Usage
$rulesSet = [
    'field0' => 'required-with:field1',
    'field1' => 'required-with:field0'
];
```

### Required With All
The field is required if all the other fields are not `null`.
```php
// Example Usage
$rulesSet = [
    'field0' => 'required-with-all:field1,field2',
    'field1' => 'is:int'
    'field2' => 'in:alpha,beta'
];
```

### Required With Any
The field is required if any of the other fields are not `null`.
```php
// Example Usage
$rulesSet = [
    'field0' => 'required-with-any:field1,field2',
    'field1' => 'is:int'
    'field2' => 'in:alpha,beta'
];
```

### Required Without
The field is required if the other field is `null`.
```php
// Example Usage
$rulesSet = [
    'field0' => 'required-without:field1',
    'field1' => 'required-without:field0'
];
```


### Equals
The field's value must be equal to the other specified field's value.
```php
// Example Usage
$rulesSet = [
    'field0' => 'equals:field1',
    'field1' => 'required'
];
```

### Not Equals
The field's value must not equal the other specified field's value.
```php
// Example Usage
$rulesSet = [
    'field0' => 'not-equals:field1',
    'field1' => 'required'
];
```

### Identical
The field's value must be identical the other specified field's value.
```php
// Example Usage
$rulesSet = [
    'field0' => 'identical:field1',
    'field1' => 'required'
];
```

### Not Identical
The field's value must not be identical the other specified field's value.
```php
// Example Usage
$rulesSet = [
    'field0' => 'not-identical:field1',
    'field1' => 'required'
];
```


### In
The field must equal one of the specified options.
```php
// Example Usage
$rulesSet = [
    'field' => 'in:apple,pear,orange'
];
```

### Not In
The field must not equal one of the specified options.
```php
// Example Usage
$rulesSet = [
    'field' => 'not-in:apple,pear,orange'
];
```

### Contains
The field should be an array and must contain all the specified options (may contain other values not listed).
```php
// Example Usage
$rulesSet = [
    'field' => 'contains:apple,pear,orange'
];
```

### Contains Only
The field should be an array and must contain only the specified options.
```php
// Example Usage
$rulesSet = [
    'field' => 'contains-only:apple,pear,orange'
];
```

### Min Array Count
The field should be an array and must have an `array_count` of at least the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'min-arr-count:1'
];
```

### Max Array Count
The field should be an array and must have an `array_count` of at most the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'max-arr-count:5'
];
```


### Min
The field should be numeric and must be at least the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'min:1'
];
```

### Max
The field should be numeric and must be at most the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'max:5'
];
```

### Greater Than
The field should be numeric and must have a value greater than the other field.
```php
// Example Usage
$rulesSet = [
    'field0' => 'greater-than:field1'
];
```

### Less Than
The field should be numeric and must have a value less than the other field.
```php
// Example Usage
$rulesSet = [
    'field0' => 'less-than:field1'
];
```


### Alpha
The field must only contain alphabetic characters.
```php
// Example Usage
$rulesSet = [
    'field' => 'alpha'
];
```

### Alpha Numeric
The field must only contain alphabetic and numerical characters.
```php
// Example Usage
$rulesSet = [
    'field' => 'alpha-numeric'
];
```

### Min String Length
The field should be string and must have a `strlen` of at least the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'min-str-len:3'
];
```

### Max String Length
The field should be a string and must have a `strlen` of at most the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'max-str-len:50'
];
```

### String Length
The field should be a string and must have a `strlen` of exactly the specified value.
```php
// Example Usage
$rulesSet = [
    'field' => 'str-len:10'
];
```

### Human Name
The field must be a valid human name.
```php
// Example Usage
$rulesSet = [
    'field' => 'human-name'
];
```


### Is ...
The field must be of the specified basic PHP type. There must be a corresponding `is_<type>` method.
```php
// Example Usage
$rulesSet = [
    'field' => 'is:numeric' // any basic PHP type (must have corresponding is_<type> method)
];
```


### Email
The field must be a valid email address
```php
// Example Usage
$rulesSet = [
    'field' => 'email'
];
```

### Date
The field must be a valid date in the specified format (defaults to `'Y-m-d'`).
```php
// Example Usage
$rulesSet = [
    'field' => 'date' // defaults to 'Y-m-d'
];
```

### URL
The field must be a valid URL.
```php
// Example Usage
$rulesSet = [
    'field' => 'url'
];
```

### UUID
The field must be a valid UUID (\universally unique identifier).
```php
// Example Usage
$rulesSet = [
    'field' => 'uuid'
];
```


### Card Number
The field must be a valid card number. See
http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php for more details.
```php
// Example Usage
$rulesSet = [
    'field' => 'card-number'
];
```


### Regex
The field must match the regex pattern provided as parameter.
Specify rules in an array when using this rule, especially when the regex expression contains a | character.
See https://www.php.net/preg_match for nore details.
```php
// Example Usage
$rulesSet = [
    'field' => ['regex:/^[Ff]oobar[1!]+$/']
];
```


### Not Regex
The field must not match the regex pattern provided as parameter.
Specify rules in an array when using this rule, especially when the regex expression contains a | character.
See https://www.php.net/preg_match for nore details.
```php
// Example Usage
$rulesSet = [
    'field' => ['not-regex:/^[abc]{1,3}\W+$/i']
];
```
