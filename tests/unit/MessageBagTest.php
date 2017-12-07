<?php

use SolBianca\Validator\MessageBag;

class MessageBagTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var SolBianca\Validator\MessageBag
     */
    private $messageBag;

    /**
     * @var array
     */
    protected $messages = [
        'name' => [
            'You must fill in the name field.',
            'Your name must only be letters.',
        ],
        'username' => [
            'Your username is required.',
            'Your username must be alphanumeric (with dashes and underscores permitted)',
        ],
        'password' => [
            'Your password must be greater than 6 characters.',
        ],
    ];

    protected function _before()
    {
        $this->messageBag = new MessageBag($this->messages);
    }


    public function testHas()
    {
        $this->tester->assertTrue($this->messageBag->has('name'));
        $this->tester->assertTrue($this->messageBag->has('username'));
        $this->tester->assertTrue($this->messageBag->has('password'));
        $this->tester->assertFalse($this->messageBag->has('not-in-messagebag'));
    }

    public function testFirst()
    {
        $this->tester->assertEquals('You must fill in the name field.', $this->messageBag->first('name'));
        $this->tester->assertEquals('Your username is required.', $this->messageBag->first('username'));
        $this->tester->assertEquals('You must fill in the name field.', $this->messageBag->first());
    }

    public function testGet()
    {
        $this->tester->assertEquals([
            'You must fill in the name field.',
            'Your name must only be letters.',
        ], $this->messageBag->get('name'));
    }

    public function testAll()
    {
        $this->tester->assertEquals($this->messages, $this->messageBag->all());
    }

    public function testKeys()
    {
        $this->tester->assertEquals(['name', 'username', 'password'], $this->messageBag->keys());
    }

    public function testIsEmpty()
    {
        $this->tester->assertFalse($this->messageBag->isEmpty());
        $messageBag = new MessageBag([]);
        $this->tester->assertTrue($messageBag->isEmpty());
    }

    public function testFlat()
    {
        $this->tester->assertEquals([
            'You must fill in the name field.',
            'Your name must only be letters.',
            'Your username is required.',
            'Your username must be alphanumeric (with dashes and underscores permitted)',
            'Your password must be greater than 6 characters.',
        ], $this->messageBag->flat());
    }
}