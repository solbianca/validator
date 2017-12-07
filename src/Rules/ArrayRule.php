<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class ArrayRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        return is_array($value);
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must be an array.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}