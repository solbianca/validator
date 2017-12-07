<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\ArrayRule;

class ArrayRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new ArrayRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => [], 'result' => true],
            ['value' => ['lol', 'lal'], 'result' => true],
            ['value' => true, 'result' => false],
            ['value' => 1, 'result' => false],
            ['value' => new stdClass(), 'result' => false],
            ['value' => null, 'result' => false],
            ['value' => 'array', 'result' => false],
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