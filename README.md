# PHP Validator

This is an easy to use and customisable PHP validator.


**Note: This package is under development and not recommended for production.**

## Installing

Install using Composer.

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
    echo 'Validation passed, woo hoo!';
} else {
    echo '<pre>', var_dump($v->errors()->all()), '</pre>';
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

