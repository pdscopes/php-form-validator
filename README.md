# php-form-validator
[![Build Status](https://travis-ci.org/pdscopes/php-form-validator.svg?branch=master)](https://travis-ci.org/pdscopes/php-form-validator)

Simple, extendable form validator for multi-dimensional forms

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


### Equals
The field's value must equals the other specified field's value.
```php
// Example Usage
$rulesSet = [
    'field0' => 'equals:field1',
    'field1' => 'required'
];
```

### Identical
The field's value must be identical the other specified field's value.
```php
// Example Usage
$rulesSet = [
    'field0' => 'equals:field1',
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


### Card Number
The field must be a valid card number. See
http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php for more details.
```php
// Example Usage
$rulesSet = [
    'field' => 'card-number'
];
```
