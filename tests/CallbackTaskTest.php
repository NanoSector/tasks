<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

use Yoshi2889\Tasks\CallbackTask;
use PHPUnit\Framework\TestCase;

class CallbackTaskTest extends TestCase
{
	public function testRun()
	{
		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		self::expectOutputString('Hello world');
		$result = $callbackTask->run();
		self::assertNull($result);
	}

	public function testRunWithNewTask()
	{
		$newCallbackTask = new CallbackTask(function ()
		{
			echo 'Hello again, world';
		}, 5);

		$callbackTask = new CallbackTask(function () use ($newCallbackTask)
		{
			echo 'Hello world';

			return $newCallbackTask;
		}, 5);

		self::expectOutputString('Hello world');
		$result = $callbackTask->run();
		self::assertSame($newCallbackTask, $result);
	}

	public function testCancel()
	{
		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		$expectedCallback = function () {};
		$expectedExpiryTime = 0;
		$callbackTask->cancel();

		self::assertEquals($callbackTask->getCallback(), $expectedCallback);
		self::assertEquals($callbackTask->getExpiryTime(), $expectedExpiryTime);
	}
}
