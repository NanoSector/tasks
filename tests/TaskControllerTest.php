<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

use Yoshi2889\Tasks\CallbackTask;
use Yoshi2889\Tasks\RepeatableTask;
use Yoshi2889\Tasks\TaskController;
use PHPUnit\Framework\TestCase;

class TaskControllerTest extends TestCase
{
	public function testAddTask()
	{
		$loop = \React\EventLoop\Factory::create();
		$taskController = new TaskController($loop);

		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		self::assertTrue($taskController->add($callbackTask));
		self::assertFalse($taskController->add($callbackTask));
		self::assertTrue($taskController->exists($callbackTask));
	}

	public function testRemoveTask()
	{
		$loop = \React\EventLoop\Factory::create();
		$taskController = new TaskController($loop);

		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world';
		}, 5);

		$taskController->add($callbackTask);

		self::assertTrue($taskController->remove($callbackTask));
		self::assertFalse($taskController->exists($callbackTask));
		self::assertFalse($taskController->remove($callbackTask));
	}

	public function testRunTasks()
	{
		$loop = \React\EventLoop\Factory::create();
		$taskController = new TaskController($loop);

		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world...';
		}, 2);

		self::assertTrue($taskController->add($callbackTask));

		$callbackTask2 = new CallbackTask(function ()
		{
			echo 'again!';
		}, 3);

		self::assertTrue($taskController->add($callbackTask2));

		sleep(4);

		self::expectOutputString('Hello world...again!');
		$taskController->runTasks();

		self::assertFalse($taskController->exists($callbackTask));
		self::assertFalse($taskController->exists($callbackTask2));
	}

	public function testRunTasksWithNonExpiredTask()
	{
		$loop = \React\EventLoop\Factory::create();
		$taskController = new TaskController($loop);

		// Here we test running of a task which should be triggered,
		// and a task which should not be triggered yet.
		$callbackTask = new CallbackTask(function ()
		{
			echo 'Hello world...';
		}, 2);

		self::assertTrue($taskController->add($callbackTask));

		$callbackTask2 = new CallbackTask(function ()
		{
			echo 'again!';
		}, 10);

		self::assertTrue($taskController->add($callbackTask2));

		sleep(4);

		self::expectOutputString('Hello world...');
		$taskController->runTasks();

		self::assertFalse($taskController->exists($callbackTask));
		self::assertTrue($taskController->exists($callbackTask2));
	}

	public function testRunRepeatableTask()
	{
		$loop = \React\EventLoop\Factory::create();
		$taskController = new TaskController($loop);

		$callbackTask = new CallbackTask(function ()
		{
			echo 'Test';
		}, 1);

		$repeatableTask = new RepeatableTask($callbackTask, 1);

		$taskController->add($repeatableTask);

		sleep(1);

		self::expectOutputString('Test');
		$taskController->runTasks();

		sleep(1);

		self::expectOutputString('TestTest');
		$taskController->runTasks();
	}

    public function testRemoveAll()
    {
        $loop = \React\EventLoop\Factory::create();
        $taskController = new TaskController($loop);

        $callbackTask = new CallbackTask(function ()
        {
            echo 'Test';
        }, 1);
        
        $taskController->add($callbackTask);
        
        $taskController->removeAll();
        
        self::assertFalse($taskController->exists($callbackTask));
        self::assertEquals(0, $callbackTask->getExpiryTime());
	}
}
