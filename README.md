# PHP Validator

This is an easy to use and customisable PHP validator.


**Note: This package is under development and not recommended for production.**

[![Build Status](https://travis-ci.org/solbianca/validator.svg?branch=master)](https://travis-ci.org/solbianca/validator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/solbianca/validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/solbianca/validator/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/7c211c8845a63229e8cd/maintainability)](https://codeclimate.com/github/solbianca/validator/maintainability)

## Installing

Install via  composer

````bash
composer require solbianca/validator
````

or add to composer.json

```json
{
    "require": {
        "solbianca/php-validator": "1.*"
    }
}
```

## Basic usage

```php
use SolBianca\Validator;

$validator = new Validator;

$validator->validate([
    'age' => ['value' => 20, 'rules' => ['required', 'int']],
    'name' => ['value' => 'John Doe', 'rules' => ['required']],
]);

if($validator->passed()) {
    echo 'Validation passed!';
} else {
    var_dump($validator->errors()->all();
}
```

## Adding custom rules

Adding custom rules is simple. It can be any callable or object which implement `SolBianca\Validator\Interfaces\RuleInterface`

If the callable returns false, the rule fails.

```php
$validator->addRule('sex', function ($value) {
    return in_array($value, ['male', 'female']);
})->addRuleMessage('sex', 'Field `{field}` must be male or female. Given value `{value}`.');

$validator->validate([
    'fruit' => ['value' => 'male', 'rules' => ['sex']],
]);
```

```php
class SomeRule implements SolBianca\Validator\Interfaces\RuleInterface 
{
    // some code
}

// You can add as a string
$validator->addRule('sex', SomeRule::class);

// or as an object
$validator->addRule('sex', new SomeRule());
```

## Rewrite rules

Validator have useful default rules as `int`, `required`  and many more. You can rewrite any rule by your own.

```php
$validator->addRule('int', function ($value) {
    return (is_int($value)) && $value > 0);
})->addRuleMessage('sex', 'Field `{field}` must be integer ang greater than zero.');

$validator->validate([
    'fruit' => ['value' => 'male', 'rules' => ['sex']],
]);
```

## Adding custom error messages

You can add custom error messages for any rule

```php
$validator->addRuleMessage('required', 'You better fill in the {field} field, or else.');
```

## Adding rule messages in bulk

```php
$v->addRuleMessages([
    'required' => 'You better fill in the {field} field, or else.',
    'int'      => 'The {field} needs to be an integer, but I found {value}.',
]);
```

## Using Field Aliases

Field Aliases helps you format any error messages without showing weird form names or the need to create a custom error.

```php
$validator->validate([
    'username_box' => ['value' => '', 'rules' => ['required'], 'alias' => 'Username']
]);

// Error output: "Field `Username` is required."
```

## Rules

#### Array

If the value is an array.

````php
$validator->validate([
    'some_input' => ['value' => [10, 20], 'rules' => ['array']],
]);
````

#### Between

Checks if the value is within the intervals defined. This check is inclusive, so 5 is between 5 and 10.

````php
$validator->validate([
    'some_input' => ['value' => 5, 'rules' => ['between' => [5, 10]]],
]);
````

#### Bool

If the value is a boolean.

````php
$validator->validate([
    'some_input' => ['value' => true, 'rules' => ['bool']],
]);
````

#### Email

If the value is a valid email.

````php
$validator->validate([
    'some_input' => ['value' => 'mail@example.com', 'rules' => ['email']],
]);
````

#### Int

If the value is an integer, including numbers within strings. 1 and '1' are both classed as integers.

````php
$validator->validate([
    'some_input' => ['value' => 42, 'rules' => ['int']],
]);
````

#### Ip

If the value is a valid IP address.

````php
$validator->validate([
    'some_input' => ['value' => '127.0.0.1', 'rules' => ['ip']],
]);
````

#### Matches

Checks if one given input matches the other. For example, checking if password matches password_confirm.

````php
$validator->validate([
    'some_input' => ['value' => 1, 'rules' => ['int', 'matches' => 'other_input']],
    'other_input' => ['value' => 1, 'rules' => ['int']]
]);
````

#### Max

Check if string length is less than or equal to given int. To check the size of a number, pass the optional number option.

````php
$validator->validate([
    'some_input' => ['value' => 5, 'rules' => ['max' => 10]],
    'other_input' => ['value' => 0.5, 'rules' => ['max' => [1.0, 'number']]],
]);
````

#### Mix

Check if string length is greater than or equal to given int. To check the size of a number, pass the optional number option.

````php
$validator->validate([
    'some_input' => ['value' => 5, 'rules' => ['min' => 1]],
    'other_input' => ['value' => 0.5, 'rules' => ['min' => [0.0, 'number']]],
]);
````

#### Number

If the value is a number, including numbers within strings.

> Numeric strings consist of optional sign, any number of digits, optional decimal part and optional exponential part. Thus +0123.45e6 is a valid numeric value. Hexadecimal (e.g. 0xf4c3b00c), Binary (e.g. 0b10100111001), Octal (e.g. 0777) notation is allowed too but only without sign, decimal and exponential part.

````php
$validator->validate([
    'some_input' => ['value' => '5', 'rules' => ['number']],
]);
````

#### Regex

If the given input has a match for the regular expression given.

````php
$validator->validate([
    'some_input' => ['value' => 'bag', 'rules' => ['regex' => '/b[aeiou]g/']],
]);
````

#### Required

If the value is present.

````php
$validator->validate([
    'some_input' => ['value' => true, 'rules' => ['required']],
]);
````

#### Url

If the value is formatted as a valid URL.

````php
$validator->validate([
    'some_input' => ['value' => 'http://example.com', 'rules' => ['url']],
]);
````