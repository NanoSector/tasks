<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

namespace Yoshi2889\Tasks;

use React\EventLoop\LoopInterface;

class TaskController
{
	/**
	 * @var int
	 */
	protected $loopInterval = 1;

	/**
	 * @var TaskInterface[]
	 */
	protected $tasks = [];

	/**
	 * TaskController constructor.
	 *
	 * @param LoopInterface $loop
	 */
	public function __construct(LoopInterface $loop)
	{
		$loop->addPeriodicTimer($this->loopInterval, [$this, 'runTasks']);
	}

	/**
	 * @param TaskInterface $task
	 *
	 * @return bool
	 */
	public function add(TaskInterface $task): bool
	{
		if ($this->exists($task))
			return false;

		$this->tasks[] = $task;

		return true;
	}

	/**
	 * @param TaskInterface $task
	 *
	 * @return bool
	 */
	public function remove(TaskInterface $task): bool
	{
		if (!$this->exists($task))
			return false;

		unset($this->tasks[array_search($task, $this->tasks)]);

		return true;
	}

    public function removeAll()
    {
        foreach ($this->tasks as $task)
        {
            $task->cancel();
            $this->remove($task);
        }
	}

	/**
	 * @param TaskInterface $task
	 *
	 * @return bool
	 */
	public function exists(TaskInterface $task): bool
	{
		return in_array($task, $this->tasks);
	}

	public function runTasks()
	{
		foreach ($this->tasks as $task)
		{
			if (time() < $task->getExpiryTime())
				continue;

			$result = $task->run();

			// It is removed first.
			$this->remove($task);

			if ($result instanceof TaskInterface)
				$this->add($result);
		}
	}
}