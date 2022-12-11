<?php

namespace Scripts\CLI;

class TextFormatter
{
	// Foreground
	public static $DEFAULT_FOREGROUND = 39;
	public static $BLACK_FOREGROUND = 30;
	public static $RED_FOREGROUND = 31;
	public static $GREEN_FOREGROUND = 32;
	public static $YELLOW_FOREGROUND = 33;
	public static $BLUE_FOREGROUND = 34;
	public static $MAGENTA_FOREGROUND = 35;
	public static $CYAN_FOREGROUND = 36;
	public static $LIGHT_GRAY_FOREGROUND = 37;
	public static $DARK_GRAY_FOREGROUND = 90;
	public static $LIGHT_RED_FOREGROUND = 91;
	public static $LIGHT_GREEN_FOREGROUND = 92;
	public static $LIGHT_YELLOW_FOREGROUND = 93;
	public static $LIGHT_BLUE_FOREGROUND = 94;
	public static $LIGHT_MAGENTA_FOREGROUND = 95;
	public static $LIGHT_CYAN_FOREGROUND = 96;
	public static $WHITE_FOREGROUND = 97;

	// Background
	public static $DEFAULT_BACKGROUND = 49;
	public static $BLACK_BACKGROUND = 40;
	public static $RED_BACKGROUND = 41;
	public static $GREEN_BACKGROUND = 42;
	public static $YELLOW_BACKGROUND = 43;
	public static $BLUE_BACKGROUND = 44;
	public static $MAGENTA_BACKGROUND = 45;
	public static $CYAN_BACKGROUND = 46;
	public static $LIGHT_GRAY_BACKGROUND = 47;
	public static $DARK_GRAY_BACKGROUND = 100;
	public static $LIGHT_RED_BACKGROUND = 101;
	public static $LIGHT_GREEN_BACKGROUND = 102;
	public static $LIGHT_YELLOW_BACKGROUND = 103;
	public static $LIGHT_BLUE_BACKGROUND = 104;
	public static $LIGHT_MAGENTA_BACKGROUND = 105;
	public static $LIGHT_CYAN_BACKGROUND = 106;
	public static $WHITE_BACKGROUND = 107;
	
	// Format
	public static $BOLD_FORMAT = 1;
	public static $DIM_FORMAT = 2;
	public static $UNDERLINED_FORMAT = 4;
	public static $BLINK_FORMAT = 5;
	public static $REVERSE_FORMAT = 7;
	public static $HIDDEN_FORMAT = 8;

	/**
	 * Format string with supplied types
	 */
	public static function Format(string $text, array $types)
	{
		return "\033[" . join(';', $types) . "m$text\033[0m\n";
	}
}
