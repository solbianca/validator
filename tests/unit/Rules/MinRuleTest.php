<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\MinRule;

class MinRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new MinRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => '', 'args' => [0], 'result' => true],
            ['value' => 10, 'args' => [5, 'number'], 'result' => true],
            ['value' => 'abc', 'args' => [1], 'result' => true],
            ['value' => 'абв', 'args' => [1], 'result' => true],
            ['value' => 10, 'args' => [20, 'number'], 'result' => false],
            ['value' => 'abcabc', 'args' => [10], 'result' => false],
            ['value' => 'абвабв', 'args' => [10], 'result' => false],
            ['value' => '', 'args' => [0], 'result' => true],
            ['value' => '', 'args' => [0, 'number'], 'result' => true],
            ['value' => true, 'args' => [1], 'result' => true],
            ['value' => true, 'args' => [1, 'number'], 'result' => true],
            ['value' => false, 'args' => [0], 'result' => true],
            ['value' => false, 'args' => [0, 'number'], 'result' => true],
        ];
    }

    /**
     * @dataProvider  valueProvider
     */
    public function testRule($value, $args, $result)
    {
        $this->tester->assertTrue($this->rule->run($value, [], $args) === $result);
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