<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class BetweenRule implements RuleInterface
{

    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return ($value >= $args[0] && $value <= $args[1]) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must be between `{$0}` and `{$1}``.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}