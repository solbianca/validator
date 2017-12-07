<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\UrlRule;

class UrlRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new UrlRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => 'http://www.duckduckgo.com', 'result' => true],
            ['value' => 'http://codecourse.com', 'result' => true],
            ['value' => 'http://127.0.0.1', 'result' => true],
            ['value' => 'ftp://127.0.0.1', 'result' => true],
            ['value' => 'ssl://codecourse.com', 'result' => true],
            ['value' => 'ssl://127.0.0.1', 'result' => true],
            ['value' => 'www.com', 'result' => false],
            ['value' => 'duckduckgo.com', 'result' => false],
            ['value' => 'www.duckduckgo', 'result' => false],
            ['value' => '127.0.0.1', 'result' => false],
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