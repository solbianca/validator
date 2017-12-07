<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class BoolRule implements RuleInterface
{

    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        return is_bool($value);
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must be a boolean.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}