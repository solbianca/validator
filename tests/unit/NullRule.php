<?php


class NullRule implements \SolBianca\Validator\Interfaces\RuleInterface
{

    public function run($value, array $input, array $args): bool
    {
        return true;
    }

    public function errorMessage(): string
    {

        return 'error';
    }

    public function canSkip(): bool
    {
        return true;
    }
}