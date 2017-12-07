<?php

use SolBianca\Validator\Interfaces\RuleInterface;
use SolBianca\Validator\Rules\EmailRule;

class EmailRuleTest extends \Codeception\Test\Unit
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
        $this->rule = new EmailRule();
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['value' => 'test@mail.com', 'result' => true],
            ['value' => 'test@mail.', 'result' => false],
            ['value' => 'test@mail', 'result' => false],
            ['value' => 'test@.com', 'result' => false],
            ['value' => '@mail.com', 'result' => false],
            ['value' => 'test#mail.com', 'result' => false],
            ['value' => '', 'result' => false],
            ['value' => true, 'result' => false],
            ['value' => ['test@mail.com'], 'result' => false],
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