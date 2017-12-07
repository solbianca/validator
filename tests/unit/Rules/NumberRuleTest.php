<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\NumberRule;

class NumberRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new NumberRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => 1, 'result' => true],
            ['value' => 1.0, 'result' => true],
            ['value' => '1', 'result' => true],
            ['value' => '1.0', 'result' => true],
            ['value' => '1.0a', 'result' => false],
            ['value' => '1a', 'result' => false],
            ['value' => '', 'result' => false],
            ['value' => null, 'result' => false],
            ['value' => true, 'result' => false],
            ['value' => [1], 'result' => false],
        ];
    }

    /**
     * @dataProvider  valueProvider
     */
    public function testRule($value, $result)
    {
        $this->tester->assertTrue($this->rule->run($value, [], []) === $result);
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