<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\BoolRule;

class BoolRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new BoolRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => true, 'result' => true],
            ['value' => false, 'result' => true],
            ['value' => 1, 'result' => false],
            ['value' => 0, 'result' => false],
            ['value' => null, 'result' => false],
            ['value' => 'true', 'result' => false],
            ['value' => '', 'result' => false],
            ['value' => [], 'result' => false],
            ['value' => [true], 'result' => false],
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