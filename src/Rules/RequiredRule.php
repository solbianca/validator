<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Interfaces\RuleInterface;

class RequiredRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        $value = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value);
        return !empty($value);
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` is required.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }
}