# Task Controller
[![Build Status](https://scrutinizer-ci.com/g/Yoshi2889/tasks/badges/build.png)](https://scrutinizer-ci.com/g/Yoshi2889/tasks/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Yoshi2889/tasks/badges/quality-score.png)](https://scrutinizer-ci.com/g/Yoshi2889/tasks/?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/Yoshi2889/tasks/badges/coverage.png)](https://scrutinizer-ci.com/g/Yoshi2889/tasks/code-structure/master/code-coverage)
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
```

### Repeatable Tasks
A `RepeatableTask` instance is a Task which runs its child task on an interval. Before a `RepeatableTask` starts
 running the child task, it first checks if its expiry time has passed. If it has not, it will not run the task.
 
For example, to create a new `RepeatableTask` that runs the previous `CallbackTask` every 5 seconds:
```php
$repeatableTask = new \Yoshi2889\Tasks\RepeatableTask($callbackTask, 5);
```

This task would only start running after the 10 seconds defined before in the `CallableTask` have passed.

### Cancelling Tasks
Tasks can be cancelled prematurely. How a cancel is handled depends on the task type.
For instance, if we cancel a RepeatableTask, it will internally cancel its child task and stop repeating,
after which it will be discarded:

```php
$repeatableTask->cancel();
```

However, if we were to cancel a `CallbackTask`, it will just be put in a state where it can no longer be run
 by the `TaskController` and will thus be discarded on the next run.
 
`TaskController` must never cancel tasks on its own, this is up to the user.
 
### Discarding Tasks
By default, a Task which does not return a new Task on its run() method will be discarded.
However, if a Task does pass back a new Task, the original Task itself gets discarded, but
the Task instance which is passed back will be added in its place. We can observe this with
the following snippet:

```php
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

Discarded tasks will be removed from the `TaskController`.

## Implementing custom Tasks
The `TaskController` accepts any class that implements the `TaskInterface` interface. This interface contains the following methods:

* `getExpiryTime(): int`: Gets the UNIX timestamp on which the task should be run and discarded afterwards.
* `run(): ?TaskInterface`: Runs the actual task. Return an object implementing `TaskInterface` to insert a new task, or `null`
 to discard the current task.
* `cancel(): void`: Used to cancel the task, or to bring it in a state where it cannot be run. It is a good idea to have
`getExpiryTime()` always return 0 after this method is called so that the task will be discarded.

## License
This code is released under the MIT License. Please see `LICENSE` to read it.