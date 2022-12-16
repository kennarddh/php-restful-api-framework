<?php

namespace Scripts\Console;

class ConsoleSingleton
{
	public static Console $console;

	public static function RegisterConsole(array $argv = [], $output = null, $error = null)
	{
		if (!isset(self::$console)) {
			self::$console = new Console($argv, $output, $error);
		}
	}

	public static function GetConsole(): Console
	{
		return self::$console;
	}
}
