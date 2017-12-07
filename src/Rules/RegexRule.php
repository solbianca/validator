<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class RegexRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        return (bool)preg_match($args[0], $value);
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` was not in the correct format.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}