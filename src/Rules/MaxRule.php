<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class MaxRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        $number = isset($args[1]) && $args[1] === 'number';
        if ($number) {
            return (float)$value <= (float)$args[0];
        }
        return mb_strlen((string)$value) <= (int)$args[0];
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must be a maximum of `{$0}`.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}