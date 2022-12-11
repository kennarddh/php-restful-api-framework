<?php

namespace Scripts\Commands;

use Scripts\CLI\CLI;

abstract class BaseCommand
{
	/**
	 * Command name
	 */

	public string $name;

	/**
	 * Command description
	 */
	public string $description;

	/**
	 * Private
	 * 
	 * Internal use only
	 * 
	 * used for refrence to current cli instance
	 */
	public CLI $cli;

	/** 
	 * Execute
	 */
	abstract public function execute();

	/** 
	 * Private
	 * 
	 * Internal use only
	 * 
	 * Inject cli instance to command
	 */
	public function injectCLI(CLI $cli): void
	{
		$this->cli = $cli;
	}
}
