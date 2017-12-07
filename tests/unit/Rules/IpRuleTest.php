<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\IpRule;

class IpRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new IpRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => '8.8.8.8', 'result' => true],
            ['value' => 'fe80:1:2:3:a:bad:1dea:dad', 'result' => true],
            ['value' => '', 'result' => false],
            ['value' => 'fe80:1:2:3:a:bad:1dea', 'result' => false],
            ['value' => 'fe80:1::3:a:bad:1dea:dad', 'result' => true],
            ['value' => [], 'result' => false],
            ['value' => true, 'result' => false],
            ['value' => 1, 'result' => false],
            ['value' => null, 'result' => false],
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