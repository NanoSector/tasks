<?php
/**
 * Created by PhpStorm.
 * User: rick2
 * Date: 10-7-2017
 * Time: 18:09
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
}
