<?php

require_once 'NullRule.php';

use SolBianca\Validator\Validator;
use SolBianca\Validator\Rules\IntRule;
use SolBianca\Validator\Exceptions\ValidatorRuleException;
use SolBianca\Validator\Interfaces\MessageBagInterface;
use SolBianca\Validator\Interfaces\ValidatorInterface;
use SolBianca\Validator\Exceptions\ValidatorExceptions;

class ValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \SolBianca\Validator\Validator
     */
    private $validator;

    /**
     * Initiate before test
     */
    protected function _before()
    {
        $this->validator = new \SolBianca\Validator\Validator();
    }

    public function testValidate()
    {
        $this->validator->addRule('sex', function ($value) {
            return in_array($value, ['male', 'female']);
        });
        $this->validator->addRuleMessage('sex', 'Field `{field}` must be male or female. Given value `{value}`.');
        $result = $this->validator->validate([
            'age' => ['value' => 20, 'rules' => ['required', 'int'], 'alias' => 'Age'],
            'name' => ['value' => 'John Doe', 'rules' => ['required']],
            'sex' => [
                'value' => 'male',
                'rules' => ['sex'],
                'alias' => 'gender',
            ],
        ]);
        $this->tester->assertInstanceOf(ValidatorInterface::class, $result);
        $this->tester->assertTrue($this->validator->passed());
        $messageBag = $this->validator->errors();
        $this->tester->assertInstanceOf(MessageBagInterface::class, $messageBag);
    }

    public function testValidateRuleWithArgs()
    {
        $this->validator->addRule('test', function ($value, $inputs, $args) {
            return false;
        });
        $this->validator->addRuleMessage('test', '{$0},{$1}');
        $this->validator->validate(['test' => ['value' => 'lol', 'rules' => ['test' => [10, 20]]]]);
        $messageBag = $this->validator->errors();
        $this->tester->assertEquals("10,20", $messageBag->get('test')[0]);
    }

    public function testValidationNotPassed()
    {
        $this->validator->addRule('sex', function ($value) {
            return in_array($value, ['male', 'female']);
        });
        $this->validator->addRuleMessage('sex', 'Field `{field}` must be male or female. Given value `{value}`.');
        $result = $this->validator->validate([
            'age' => ['value' => 'lol', 'rules' => ['required', 'int'], 'alias' => 'Age'],
            'name' => ['value' => null, 'rules' => ['required']],
            'sex' => [
                'value' => 'lol',
                'rules' => ['sex'],
                'alias' => 'gender',
            ],
        ]);
        $this->tester->assertInstanceOf(ValidatorInterface::class, $result);
        $this->tester->assertFalse($this->validator->passed());
        $messageBag = $this->validator->errors();
        $this->tester->assertInstanceOf(MessageBagInterface::class, $messageBag);
        $this->tester->assertEquals("Field `Age` must be an integer.",
            $messageBag->get('age')[0]);
        $this->tester->assertEquals("Field `gender` must be male or female. Given value `lol`.",
            $messageBag->get('sex')[0]);
    }

    public function testValidateBadData()
    {
        $this->tester->expectException(ValidatorExceptions::class, function () {
            $this->validator->validate(['age' => ['value' => 20, 'rules' => 'required']]);
        });

        $this->tester->expectException(ValidatorExceptions::class, function () {
            $this->validator->validate(['age' => [20, 'rules' => ['required']]]);
        });

        $this->tester->expectException(ValidatorExceptions::class, function () {
            $this->validator->validate(['age' => ['value' => 20]]);
        });

        $this->tester->expectException(ValidatorExceptions::class, function () {
            $this->validator->validate(['age' => 20, 'rules' => ['required']]);
        });
    }

    public function testRuleWithoutErrorMessage()
    {
        $this->tester->expectException(ValidatorExceptions::class, function () {
            $validator = new \SolBianca\Validator\Validator();
            $validator->addRule('test', function ($value) {
                return false;
            });
            $validator->validate(['age' => ['value' => 20, 'rules' => ['test'], 'alias' => 'Age']]);
            $validator->errors();
        });
    }

    public function testGetEmptyMessageBag()
    {
        $messageBag = $this->validator->errors();
        $this->tester->assertInstanceOf(MessageBagInterface::class, $messageBag);
    }

    public function testAddRuleMessage()
    {
        $messages = $this->getRuleErrorMessages();
        $this->tester->assertArrayNotHasKey('test', $messages);

        $this->validator->addRuleMessage('test', 'error');
        $messages = $this->getRuleErrorMessages();
        $this->tester->assertArrayHasKey('test', $messages);
        $this->tester->assertEquals('error', $messages['test']);
    }

    public function testAddRuleMessageWithInvalidRuleName()
    {
        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRuleMessage('', 'error');
        });
    }

    public function testAddRuleMessageWithInvalidRuleMessage()
    {
        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRuleMessage('test', '');
        });
    }

    public function testAddRulesMessages()
    {
        $messages = $this->getRuleErrorMessages();
        $this->tester->assertArrayNotHasKey('test1', $messages);
        $this->tester->assertArrayNotHasKey('test2', $messages);

        $this->validator->addRuleMessages(['test1' => 'error1', 'test2' => 'error2']);
        $messages = $this->getRuleErrorMessages();
        $this->tester->assertArrayHasKey('test1', $messages);
        $this->tester->assertArrayHasKey('test2', $messages);
        $this->tester->assertEquals('error1', $messages['test1']);
        $this->tester->assertEquals('error2', $messages['test2']);
    }

    public function testAddInvalidRulesMessages()
    {
        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRuleMessages(['' => 'error1', 'test2' => 'error2']);
        });

        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRuleMessages(['' => 'error1', 'test2' => '']);
        });
    }

    public function testDefaultRules()
    {
        $rules = $this->getValidatorRulesMap();
        $this->tester->assertArrayNotHasKey('test', $rules);
        $this->tester->assertEquals(IntRule::class, $rules['int']);
    }

    public function testAddRuleAsClass()
    {
        $this->validator->addRule('test', new NullRule());
        $rules = $this->getValidatorRulesMap();
        $this->tester->assertArrayHasKey('test', $rules);
        $this->tester->assertInstanceOf(NullRule::class, $rules['test']);
    }

    public function testAddRuleAsString()
    {
        $this->validator->addRule('test', NullRule::class);
        $rules = $this->getValidatorRulesMap();
        $this->tester->assertArrayHasKey('test', $rules);
        $this->tester->assertEquals(NullRule::class, $rules['test']);
    }

    public function testAddRuleAsCalback()
    {
        $this->validator->addRule('test', function () {
            return true;
        });
        $rules = $this->getValidatorRulesMap();
        $this->tester->assertArrayHasKey('test', $rules);
        $this->tester->assertInstanceOf(Closure::class, $rules['test']);
    }

    public function testRewriteRule()
    {
        $rules = $this->getValidatorRulesMap();
        $this->tester->assertArrayHasKey('int', $rules);
        $this->tester->assertEquals(IntRule::class, $rules['int']);

        $this->validator->addRule('int', NullRule::class);
        $rules = $this->getValidatorRulesMap();
        $this->tester->assertArrayHasKey('int', $rules);
        $this->tester->assertEquals(NullRule::class, $rules['int']);
    }

    public function testAddRulesWithEmptyName()
    {
        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRule('', NullRule::class);
        });
    }

    public function testAddInvalidRules()
    {
        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRule('test', new stdClass());
        });

        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRule('test', null);
        });

        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRule('test', true);
        });

        $this->tester->expectException(ValidatorRuleException::class, function () {
            $this->validator->addRule('test', []);
        });
    }

    /**
     * @return array
     */
    private function getValidatorRulesMap()
    {
        $reflection = new ReflectionClass(Validator::class);
        $reflectionProperty = $reflection->getProperty('rulesMap');
        $reflectionProperty->setAccessible(true);
        $rulesMap = $reflectionProperty->getValue($this->validator);
        return $rulesMap;
    }

    /**
     * @return array
     */
    private function getRuleErrorMessages()
    {
        $reflection = new ReflectionClass(Validator::class);
        $reflectionProperty = $reflection->getProperty('messages');
        $reflectionProperty->setAccessible(true);
        $messages = $reflectionProperty->getValue($this->validator);
        return $messages;
    }
}