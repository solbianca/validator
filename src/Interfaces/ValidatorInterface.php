<?php

namespace SolBianca\Validator\Interfaces;


use SolBianca\Validator\Exceptions\ValidatorExceptions;
use SolBianca\Validator\Exceptions\ValidatorRuleException;

interface ValidatorInterface
{
    /**
     * Kick off the validation using input and rules.
     *
     * @param array $dataToValidate
     * @return ValidatorInterface
     * @throws ValidatorExceptions
     */
    public function validate(array $dataToValidate): ValidatorInterface;

    /**
     * @return bool
     */
    public function passed(): bool;

    /**
     * Gather errors, format them and return them.
     *
     * @return MessageBagInterface
     */
    public function errors(): MessageBagInterface;

    /**
     * Add error message for rule
     *
     * @param string $rule
     * @param string $message
     * @return ValidatorInterface
     */
    public function addRuleMessage(string $rule, string $message): ValidatorInterface;

    /**
     * Add errors messages for rules
     * Messages is array ['rule name' => 'rule error message']
     *
     * @param array $messages
     * @return ValidatorInterface
     */
    public function addRuleMessages(array $messages): ValidatorInterface;

    /**
     * Add or rewrite rule for validation
     *
     * @param string $name
     * @param string|RuleInterface|callable $rule
     * @return ValidatorInterface
     * @throws ValidatorRuleException
     */
    public function addRule(string $name, $rule): ValidatorInterface;
}