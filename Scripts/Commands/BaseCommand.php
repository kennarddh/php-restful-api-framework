<?php

namespace Scripts\Commands;

use Scripts\CLI\CLI;

abstract class BaseCommand
{
	/**
	 * Command name
	 */

	public static string $name;

	/**
	 * Command description
	 */
	public static string $description;

	/**
	 * Internal use only
	 * 
	 * Used for refrence to current cli instance
	 */
	public CLI $cli;

	/** 
	 * Execute
	 */
	abstract public function execute();

	/** 
	 * Internal use only
	 * 
	 * Constructor
	 * 
	 * Inject cli instance to command
	 */
	public function __construct(CLI $cli)
	{
		$this->cli = $cli;
	}
}
