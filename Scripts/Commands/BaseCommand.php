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
	 * Arguments for help command
	 */
	public static $arguments = [];
}
