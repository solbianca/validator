<?php


namespace SolBianca\Validator;


use SolBianca\Validator\Exceptions\ValidatorExceptions;
use SolBianca\Validator\Exceptions\ValidatorRuleException;
use SolBianca\Validator\Interfaces\MessageBagInterface;
use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Interfaces\ValidatorInterface;
use SolBianca\Validator\Rules\ArrayRule;
use SolBianca\Validator\Rules\BetweenRule;
use SolBianca\Validator\Rules\BoolRule;
use SolBianca\Validator\Rules\EmailRule;
use SolBianca\Validator\Rules\IntRule;
use SolBianca\Validator\Rules\IpRule;
use SolBianca\Validator\Rules\MatchesRule;
use SolBianca\Validator\Rules\MaxRule;
use SolBianca\Validator\Rules\MinRule;
use SolBianca\Validator\Rules\NumberRule;
use SolBianca\Validator\Rules\RegexRule;
use SolBianca\Validator\Rules\RequiredRule;
use SolBianca\Validator\Rules\UrlRule;

class Validator implements ValidatorInterface
{
    private $rulesMap = [
        'array' => ArrayRule::class,
        'between' => BetweenRule::class,
        'bool' => BoolRule::class,
        'email' => EmailRule::class,
        'int' => IntRule::class,
        'ip' => IpRule::class,
        'matches' => MatchesRule::class,
        'max' => MaxRule::class,
        'min' => MinRule::class,
        'number' => NumberRule::class,
        'regex' => RegexRule::class,
        'required' => RequiredRule::class,
        'url' => UrlRule::class,
    ];

    /**
     * @var RuleInterface[]
     */
    private $instantiatedRules = [];

    /**
     * @var array
     */
    private $before = [];

    /**
     * @var array
     */
    private $after = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $dataToValidate = [];

    /**
     * @var array
     */
    private $messages = [];

    /**
     * {@inheritdoc}
     */
    public function validate(array $dataToValidate): ValidatorInterface
    {
        $this->runBeforeCallbacks();
        $this->dataToValidate = $dataToValidate;

        foreach ($dataToValidate as $field => $fieldData) {
            if (!$this->isFieldDefinitionValid($fieldData)) {
                throw new ValidatorExceptions("Bad field definition.");
            }

            $value = $fieldData['value'];
            $rules = $fieldData['rules'];

            foreach ($rules as $ruleName => $ruleArguments) {
                if (is_int($ruleName) && is_string($ruleArguments)) {
                    $ruleName = $ruleArguments;
                    $ruleArguments = [];
                } elseif (is_string($ruleName) && !is_array($ruleArguments)) {
                    $ruleArguments = [$ruleArguments];
                }

                $continue = $this->validateAgainstRule($field, $value, $ruleName, $ruleArguments);
                if (!$continue) {
                    break;
                }
            }
        }

        $this->runAfterCallbacks();

        return $this;
    }

    /**
     * @return bool
     */
    public function passed(): bool
    {
        return $this->errors()->isEmpty();
    }

    /**
     * Validates value against a specific rule and handles errors if the rule validation fails.
     *
     * @param string $field
     * @param mixed $value
     * @param string $ruleName
     * @param array $ruleArguments
     * @return bool
     */
    private function validateAgainstRule(string $field, $value, string $ruleName, array $ruleArguments): bool
    {
        $ruleToCall = $this->getRuleToCall($ruleName);
        $passed = call_user_func_array($ruleToCall, [$value, $this->dataToValidate, $ruleArguments]);
        if (!$passed) {
            $this->handleError($field, $value, $ruleName, $ruleArguments);
            return $this->canSkipRule($ruleToCall);
        }

        return true;
    }

    /**
     * Stores an error.
     *
     * @param string $field
     * @param mixed $value
     * @param string $ruleName
     * @param array $ruleArguments
     */
    private function handleError(string $field, $value, $ruleName, $ruleArguments)
    {
        $this->errors[$ruleName][] = [
            'field' => $field,
            'value' => $value,
            'args' => $ruleArguments,
        ];
    }

    /**
     * If the rule to call specifically doesn't allowing skipping, then we don't want skip the rule.
     *
     * @param array $ruleToCall
     * @return bool
     */
    private function canSkipRule(array $ruleToCall): bool
    {
        if (method_exists($ruleToCall[0], 'canSkip')) {
            return call_user_func([$ruleToCall[0], 'canSkip']);
        }

        return true;
    }

    /**
     * Check that rule field definition is valid array.
     *
     * @param $fieldDefinition
     * @return bool
     */
    private function isFieldDefinitionValid($fieldDefinition): bool
    {
        try {
            return (
                key_exists('value', $fieldDefinition)
                && (key_exists('rules', $fieldDefinition) && is_array($fieldDefinition['rules']))
            );
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Gets and instantiates a rule object, e.g. IntRule. If it has
     * already been used, it pulls from the stored rule objects.
     *
     * @param  mixed $ruleName
     * @return array|callable
     * @throws ValidatorRuleException
     */
    private function getRuleToCall(string $ruleName): array
    {
        if (isset($this->instantiatedRules[$ruleName])) {
            return [$this->instantiatedRules[$ruleName], 'run'];
        }

        if (!isset($this->rulesMap[$ruleName])) {
            throw new ValidatorRuleException("Bad rules map definition.");
        }

        $rule = $this->rulesMap[$ruleName];
        if (is_callable($rule)) {
            return [$rule, '__invoke'];
        }

        if (is_string($rule) && class_exists($rule)) {
            $ruleClass = $this->rulesMap[$ruleName];
            $rule = new $ruleClass();
        }

        if ($rule instanceof RuleInterface) {
            $this->instantiatedRules[$ruleName] = $rule;
            return [$rule, 'run'];
        }

        throw new ValidatorRuleException("Bad rule definition.");
    }

    /**
     * {@inheritdoc}
     */
    public function errors(): MessageBagInterface
    {
        $messages = [];

        foreach ($this->errors as $rule => $items) {
            foreach ($items as $item) {
                $field = $item['field'];
                $message = $this->fetchMessage($rule);
                $messages[$field][] = $this->replaceMessageFormat($message, $item);
            }
        }

        return new MessageBag($messages);
    }

    /**
     * Fetch the message for an error rule.
     *
     * @param string $ruleName
     * @return string
     * @throws ValidatorRuleException
     */
    private function fetchMessage(string $ruleName): string
    {
        if (isset($this->messages[$ruleName])) {
            return $this->messages[$ruleName];
        }

        if (isset($this->instantiatedRules[$ruleName])) {
            return $this->instantiatedRules[$ruleName]->errorMessage();
        }

        throw new ValidatorRuleException("You must define error message for rule `{$ruleName}`.");
    }

    /**
     * Replaces message variables.
     *
     * @param  string $message
     * @param  array $item
     * @return string
     * @throws ValidatorExceptions
     */
    private function replaceMessageFormat(string $message, array $item): string
    {
        if (!empty($item['args'])) {
            $args = $item['args'];
            $argReplace = array_map(function ($i) {
                return "{\${$i}}";
            }, array_keys($args));
            $args[] = count($item['args']);
            $argReplace[] = '{$#}';
            $args[] = implode(', ', $item['args']);
            $argReplace[] = '{$*}';
            $message = str_replace($argReplace, $args, $message);
        }

        if (!key_exists('value', $item) || !key_exists('field', $item)) {
            throw new ValidatorExceptions("Bad error message format.");
        }
        $value = $this->prepareValueForMessage($item['value']);
        $field = $this->prepareFieldForMessage($item['field']);

        $message = str_replace(
            ['{field}', '{value}'],
            [$field, $value],
            $message
        );

        return $message;
    }

    /**
     * @param mixed $rawValue
     * @return string
     */
    private function prepareValueForMessage($rawValue): string
    {
        if (is_scalar($rawValue)) {
            $value = (string)$rawValue;
        } else {
            $value = print_r($rawValue, true);
        }
        return $value;
    }

    /**
     * @param $rawField
     * @return string
     */
    private function prepareFieldForMessage($rawField): string
    {
        if (isset($this->dataToValidate[$rawField]['alias'])
            && is_string($this->dataToValidate[$rawField]['alias'])
            && '' !== $this->dataToValidate[$rawField]['alias']
        ) {
            return $this->dataToValidate[$rawField]['alias'];
        }
        return $rawField;
    }

    /**
     * {@inheritdoc}
     */
    public function addRuleMessage(string $rule, string $message): ValidatorInterface
    {
        if (empty($rule) || empty($message)) {
            throw new ValidatorRuleException('Properties `$rule` and `$message` can\'t be empty string.');
        }
        $this->messages[$rule] = $message;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRuleMessages(array $messages): ValidatorInterface
    {
        array_walk($messages, function (string $message, string $rule) {
            $this->addRuleMessage($rule, $message);
        });
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(string $name, $rule): ValidatorInterface
    {
        if ('' === $name) {
            throw new ValidatorRuleException("Rule name must be not empty string");
        }

        if (is_string($rule) || ($rule instanceof RuleInterface) || is_callable($rule)) {
            $this->rulesMap[$name] = $rule;
            return $this;
        }

        throw new ValidatorRuleException("Rule must be callable, full class name as string or object which implemented RuleInterface.");
    }

    /**
     * Run callbacks before validation
     */
    private function runBeforeCallbacks()
    {
        foreach ($this->before as $before) {
            call_user_func_array($before, [$this]);
        }
    }

    /**
     * Register an before validation callback.
     *
     * @param  callable $closure
     * @return ValidatorInterface
     */
    public function before(callable $closure): ValidatorInterface
    {
        $this->before[] = $closure;
        return $this;
    }

    /**
     * Run callbacks after validation
     */
    private function runAfterCallbacks()
    {
        foreach ($this->after as $after) {
            call_user_func_array($after, [$this]);
        }
    }

    /**
     * Register an after validation callback.
     *
     * @param  callable $closure
     * @return ValidatorInterface
     */
    public function after(callable $closure): ValidatorInterface
    {
        $this->after[] = $closure;
        return $this;
    }
}