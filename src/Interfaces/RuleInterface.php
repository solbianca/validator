<?php


namespace SolBianca\Validator\Interfaces;


use SolBianca\Validator\Exceptions\RuleException;

interface RuleInterface
{
    /**
     * Runs the rule to check validity. Returning false fails
     * the check and returning true passes the check.
     *
     * @param  mixed $value
     * @param  array $input
     * @param  array $args
     *
     * @return bool
     *
     * @throws RuleException
     */
    public function run($value, array $input, array $args): bool;

    /**
     * The error given if the rule fails.
     *
     * @return string
     */
    public function errorMessage(): string;

    /**
     * If the rule can be skipped, if the value given
     * to the validator is not required.
     *
     * @return bool
     */
    public function canSkip(): bool;
}