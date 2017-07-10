# Task Controller
[![Build Status](https://scrutinizer-ci.com/g/Yoshi2889/tasks/badges/build.png?b=3.0)](https://scrutinizer-ci.com/g/Yoshi2889/tasks/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Yoshi2889/tasks/badges/quality-score.png?b=3.0)](https://scrutinizer-ci.com/g/Yoshi2889/tasks/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/yoshi2889/tasks/v/stable)](https://packagist.org/packages/yoshi2889/tasks)
[![Latest Unstable Version](https://poser.pugx.org/yoshi2889/tasks/v/unstable)](https://packagist.org/packages/yoshi2889/tasks)
[![Total Downloads](https://poser.pugx.org/yoshi2889/tasks/downloads)](https://packagist.org/packages/yoshi2889/tasks)

Simple task controller supporting multiple types of tasks.

## Installation
You can install this class via `composer`:

```composer require yoshi2889/tasks```

## Usage
Create an instance of TaskController and add any instance of TaskInterface to it:

```php
<?php

$loop = React\EventLoop\Factory::create();
$taskController = new \Yoshi2889\Tasks\TaskController($loop);

// A simple callback task, run only once, which will trigger in 10 seconds:
$callbackTask = new \Yoshi2889\Tasks\CallbackTask(function ()
{
	echo 'Hello world!' . PHP_EOL;
}, 10);
// Output (after 10 seconds): Hello world!

// A repeatable task which repeats the previous task every 5 seconds.
// A repeatable task respects the child's expiry time in that it only runs the child task
// after the child's expiry time has passed. So for instance if we use the previous task,
// it will only start repeating after the 10 seconds have passed.
$repeatableTask = new \Yoshi2889\Tasks\RepeatableTask($callbackTask, 5);

// Tasks can be cancelled prematurely. How a cancel is handled depends on the task type.
// For instance, if we cancel a RepeatableTask, it will cancel its child task and stop repeating:
$repeatableTask->cancel();

// By default, a Task which does not return a new Task on its run() method will be discarded.
// However, if a Task does pass back a new Task, the original Task itself gets discarded, but
// the Task instance which is passed back will be added in its place. We can observe this with
// the following snippet:
$callbackTask = new \Yoshi2889\Tasks\CallbackTask(function ()
{
	echo 'Hello ';
	return new \Yoshi2889\Tasks\CallbackTask(function ()
	{
		echo 'world!' . PHP_EOL;
	}, 5); 
}, 5);
// Output (after 10 seconds): Hello world! 
```

## License
This code is released under the MIT License. Please see `LICENSE` to read it.