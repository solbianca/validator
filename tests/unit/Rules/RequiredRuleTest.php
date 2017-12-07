<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\RequiredRule;

class RequiredRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new RequiredRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => '1', 'result' => true],
            ['value' => 1, 'result' => true],
            ['value' => true, 'result' => true],
            ['value' => [1], 'result' => true],
            ['value' => '', 'result' => false],
            ['value' => false, 'result' => false],
            ['value' => 0, 'result' => false],
            ['value' => '0', 'result' => false],
            ['value' => null, 'result' => false],
            ['value' => [], 'result' => false],
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