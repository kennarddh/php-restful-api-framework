<?php

namespace Scripts\Commands;

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
	 * Execute
	 */
	abstract public function execute();
}
