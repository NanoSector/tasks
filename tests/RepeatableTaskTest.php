<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

use PHPUnit\Framework\TestCase;
use Yoshi2889\Tasks\CallbackTask;
use Yoshi2889\Tasks\RepeatableTask;

class RepeatableTaskTest extends TestCase
{
	public function testRun()
	{
		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		$repeatableTask = new RepeatableTask($callbackTask, 1);

		self::expectOutputString('');
		$result = $repeatableTask->run();
		self::assertEquals($repeatableTask, $result);

		sleep(5);

		self::expectOutputString('Hello world');
		$repeatableTask->run();
	}

	public function testRunWithNewChild()
	{
		$newCallbackTest = new CallbackTask(function ()
		{
			echo '... Test';
		}, 1);

		$callbackTask = new CallbackTask(function () use ($newCallbackTest)
		{
			echo 'Hello world';

			return $newCallbackTest;
		}, 1);

		$repeatableTask = new RepeatableTask($callbackTask, 1);

		sleep(2);

		self::expectOutputString('Hello world');
		$result = $repeatableTask->run();
		self::assertEquals($repeatableTask, $result);

		self::expectOutputString('Hello world... Test');
		$repeatableTask->run();
	}

	public function testReplaceRepeatInterval()
	{
		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		$repeatableTask = new RepeatableTask($callbackTask, 1);

		self::assertEquals(1, $repeatableTask->getRepeatInterval());

		$repeatableTask->setRepeatInterval(2);
		self::assertEquals(2, $repeatableTask->getRepeatInterval());
	}

	public function testCancel()
	{
		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		$repeatableTask = new RepeatableTask($callbackTask, 1);

		$expectedCallback = function () {};
		$expectedExpiryTime = 0;
		$expectedRepeatInterval = 0;

		$repeatableTask->cancel();

		self::assertEquals($callbackTask->getCallback(), $expectedCallback);
		self::assertEquals($callbackTask->getExpiryTime(), $expectedExpiryTime);
		self::assertEquals($repeatableTask->getExpiryTime(), $expectedExpiryTime);
		self::assertEquals($repeatableTask->getRepeatInterval(), $expectedRepeatInterval);
	}

	public function testCancelDiscard()
	{
		$loop = \React\EventLoop\Factory::create();
		$taskController = new \Yoshi2889\Tasks\TaskController($loop);

		$callbackTask = new CallbackTask(function () {
			echo 'Hello world';
		}, 1);

		$repeatableTask = new RepeatableTask($callbackTask, 1);
		$taskController->add($repeatableTask);

		sleep(2);

		$this->expectOutputString('Hello world');
		$taskController->runTasks();
		$this->assertTrue($taskController->exists($repeatableTask));

		$repeatableTask->cancel();
		$this->expectOutputString('Hello world');
		$taskController->runTasks();
		$this->assertFalse($taskController->exists($repeatableTask));
	}
}
