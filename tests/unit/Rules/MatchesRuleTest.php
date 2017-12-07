<?php

use SolBianca\Validator\Exceptions\RuleException;
use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\MatchesRule;

class MatchesRuleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var RuleInterface
     */
    private $rule;

    protected function _before()
    {
        $this->rule = new MatchesRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            [
                'value' => 12,
                'inputs' => ['age' => ['value' => 12, 'rules' => 'int']],
                'args' => ['age'],
                'result' => true,
            ],
            [
                'value' => 12,
                'inputs' => ['age' => ['value' => 'qwerty', 'rules' => 'required']],
                'args' => ['age'],
                'result' => false,
            ],
            [
                'value' => 12,
                'inputs' => ['age' => ['value' => '12', 'rules' => 'required']],
                'args' => ['age'],
                'result' => false,
            ],
            [
                'value' => 12,
                'inputs' => ['size' => ['value' => 12, 'rules' => 'int']],
                'args' => ['age'],
                'result' => RuleException::class,
            ],
            [
                'value' => 12,
                'inputs' => ['age' => ['value' => 12, 'rules' => 'int']],
                'args' => [],
                'result' => RuleException::class,
            ],
        ];
    }

    /**
     * @dataProvider  valueProvider
     */
    public function testRule($value, $inputs, $args, $result)
    {
        if (is_bool($result)) {
            $this->tester->assertTrue($this->rule->run($value, $inputs, $args) === $result);
        } else {
            $this->tester->expectException($result, function () use ($value, $inputs, $args) {
                $this->rule->run($value, $inputs, $args);
            });
        }
    }

    public function testErrorMessage()
    {
        $this->tester->assertNotEmpty($this->rule->errorMessage());
    }

    public function testCanSkip()
    {
        $this->tester->assertTrue($this->rule->canSkip());
    }
}