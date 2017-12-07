<?php


namespace SolBianca\Validator\Rules;


use SolBianca\Validator\Exceptions\RuleException;
use SolBianca\Validator\Interfaces\RuleInterface;
use Solbianca\VarDumper\VarDumper;

class MatchesRule implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function run($value, array $input, array $args): bool
    {
        if (!$this->isParametersValid($input, $args)) {
            return false;
        }
        return $value === $input[$args[0]]['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function errorMessage(): string
    {
        return 'Field `{field}` must match `{$0}`.';
    }

    /**
     * {@inheritdoc}
     */
    public function canSkip(): bool
    {
        return true;
    }

    /**
     * @param array $input
     * @param array $args
     * @return bool
     * @throws RuleException
     */
    private function isParametersValid(array $input, array $args): bool
    {
        if (empty($args[0])) {
            throw new RuleException("You must provide valid input field name.");
        }
        if (!isset($input[$args[0]])) {
            throw new RuleException("Input data don't contain field `{$args[0]}`.");
        }
        return true;
    }
}