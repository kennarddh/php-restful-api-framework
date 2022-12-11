<?php

namespace Scripts\CLI;

class Colorize
{
	public static $RED = 1;
	public static $YELLOW = 2;
	public static $GREEN = 3;
	public static $NORMAL = 4;

	public static function Colorize(string $text, int $type)
	{
		switch ($type) {
			case self::$RED:
				return "\033[91m$text\033[0m\n";
			case self::$YELLOW:
				return "\033[93m$text\033[0m\n";
			case self::$GREEN:
				return "\033[32m$text\033[0m\n";
			case self::$NORMAL:
				return "\033[39m$text\033[0m\n";
			default:
				return $text;
		}
	}
}
