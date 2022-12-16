<?php

namespace Scripts\Console;

class ConsoleSingleton
{
	public static Console $console;

	public static function GetConsole(): Console
	{
		global $argv;

		if (!isset(self::$console)) {
			self::$console = new Console($argv);
		}

		return self::$console;
	}
}
