<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\BetweenRule;

class BetweenRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new BetweenRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => 5, 'arg1' => 1, 'arg2' => 10, 'result' => true],
            ['value' => 15, 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => 0.5, 'arg1' => 0.1, 'arg2' => 1.0, 'result' => true],
            ['value' => 1.5, 'arg1' => 0.1, 'arg2' => 1.0, 'result' => false],
            ['value' => 1, 'arg1' => 1, 'arg2' => 10, 'result' => true],
            ['value' => 10, 'arg1' => 1, 'arg2' => 10, 'result' => true],
            ['value' => '1', 'arg1' => 1, 'arg2' => 10, 'result' => true],
            ['value' => '15', 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => '1', 'arg1' => '1', 'arg2' => '10', 'result' => true],
            ['value' => true, 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => false, 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => [], 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => '', 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => null, 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => [1], 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => 'a', 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => '1a', 'arg1' => 1, 'arg2' => 10, 'result' => false],
            ['value' => 'a1', 'arg1' => 1, 'arg2' => 10, 'result' => false],
        ];
    }

    /**
     * @dataProvider  valueProvider
     */
    public function testRule($value, $arg1, $arg2, $result)
    {
        $this->tester->assertTrue($this->rule->run($value, [], [$arg1, $arg2]) === $result);
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