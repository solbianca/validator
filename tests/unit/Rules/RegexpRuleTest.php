<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\RegexRule;

class RegexpRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new RegexRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => 'bag', 'args' => ['/b[aeiou]g/'], 'result' => true],
            ['value' => 'big', 'args' => ['/b[aeiou]g/'], 'result' => true],
            ['value' => 'banter', 'args' => ['/b[aeiou]g/'], 'result' => false],
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