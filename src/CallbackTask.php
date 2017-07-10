<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

namespace Yoshi2889\Tasks;

class CallbackTask implements TaskInterface
{
	/**
	 * @var callable
	 */
	protected $callback = null;

	/**
	 * @var int
	 */
	protected $expiryTime = 0;

	/**
	 * @var array
	 */
	protected $storedArguments = [];

	/**
	 * CallbackTask constructor.
	 *
	 * @param callable $callback
	 * @param int $time
	 * @param array $args
	 */
	public function __construct(callable $callback, int $time, array $args = [])
	{
		$this->callback = $callback;
		$this->setExpiryTime(time() + $time);
		$this->setStoredArguments($args);
	}

	public function run(): ?TaskInterface
	{
		$result = call_user_func_array($this->getCallback(), $this->getStoredArguments());

		if (!($result instanceof TaskInterface))
			return null;

		return $result;
	}

	public function cancel(): void
	{
		$this->callback = function () {};
		$this->setExpiryTime(time() - 1);
	}

	/**
	 * @return callable
	 */
	public function getCallback(): callable
	{
		return $this->callback;
	}

	/**
	 * @return int
	 */
	public function getExpiryTime(): int
	{
		return $this->expiryTime;
	}

	/**
	 * @param int $expiryTime
	 */
	public function setExpiryTime(int $expiryTime)
	{
		$this->expiryTime = $expiryTime;
	}

	/**
	 * @return array
	 */
	public function getStoredArguments(): array
	{
		return $this->storedArguments;
	}

	/**
	 * @param array $storedArguments
	 */
	public function setStoredArguments(array $storedArguments)
	{
		$this->storedArguments = $storedArguments;
	}


}