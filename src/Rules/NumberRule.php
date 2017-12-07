<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class NumberRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        return is_numeric($value);
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must be a number.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}