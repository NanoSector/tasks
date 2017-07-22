<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

namespace Yoshi2889\Tasks;


class RepeatableTask implements TaskInterface
{
	/**
	 * @var int
	 */
	protected $repeatInterval = 0;

	/**
	 * @var int
	 */
	protected $expiryTime = 0;

	/**
	 * @var TaskInterface
	 */
	protected $childTask;

	/**
	 * RepeatableTask constructor.
	 *
	 * @param TaskInterface $childTask
	 * @param int $repeatInterval
	 */
	public function __construct(TaskInterface $childTask, int $repeatInterval)
	{
		$this->expiryTime = time() + $repeatInterval;
		$this->repeatInterval = $repeatInterval;
		$this->childTask = $childTask;
	}

	/**
	 * @return null|TaskInterface
	 */
	public function run(): ?TaskInterface
	{
		if (time() >= $this->childTask->getExpiryTime())
		{
			$result = $this->childTask->run();

			if ($result instanceof TaskInterface)
				$this->childTask = $result;
		}

		$this->expiryTime = $this->getExpiryTime() + $this->getRepeatInterval();
		return $this->expiryTime > 0 ? $this : null;
	}

	public function cancel(): void
	{
		$this->childTask->cancel();
		$this->repeatInterval = 0;
		$this->expiryTime = 0;
	}

	/**
	 * @return int
	 */
	public function getRepeatInterval(): int
	{
		return $this->repeatInterval;
	}

	/**
	 * @param int $repeatInterval
	 */
	public function setRepeatInterval(int $repeatInterval)
	{
		$this->repeatInterval = $repeatInterval;
	}

	/**
	 * @return int
	 */
	public function getExpiryTime(): int
	{
		return $this->expiryTime;
	}
}