<?php

/**
 * Copyright 2017 NanoSector
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

namespace Yoshi2889\Tasks;


interface TaskInterface
{
	/**
	 * @return int
	 */
	public function getExpiryTime(): int;

	/**
	 * Pass back a new instance of TaskInterface to run a new task.
	 * @return null|TaskInterface
	 */
	public function run(): ?TaskInterface;

	/**
	 * @return void
	 */
	public function cancel(): void;
}