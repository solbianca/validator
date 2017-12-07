<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class IntRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $inputs, array $args): bool
    {
        return is_int($value);
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must be an integer.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}