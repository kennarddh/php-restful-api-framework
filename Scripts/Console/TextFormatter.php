<?php

namespace Scripts\Console;

class TextFormatter
{
	// Foreground
	const DEFAULT_FOREGROUND = 39;
	const BLACK_FOREGROUND = 30;
	const RED_FOREGROUND = 31;
	const GREEN_FOREGROUND = 32;
	const YELLOW_FOREGROUND = 33;
	const BLUE_FOREGROUND = 34;
	const MAGENTA_FOREGROUND = 35;
	const CYAN_FOREGROUND = 36;
	const LIGHT_GRAY_FOREGROUND = 37;
	const DARK_GRAY_FOREGROUND = 90;
	const LIGHT_RED_FOREGROUND = 91;
	const LIGHT_GREEN_FOREGROUND = 92;
	const LIGHT_YELLOW_FOREGROUND = 93;
	const LIGHT_BLUE_FOREGROUND = 94;
	const LIGHT_MAGENTA_FOREGROUND = 95;
	const LIGHT_CYAN_FOREGROUND = 96;
	const WHITE_FOREGROUND = 97;

	// Background
	const BLACK_BACKGROUND = 40;
	const RED_BACKGROUND = 41;
	const GREEN_BACKGROUND = 42;
	const YELLOW_BACKGROUND = 43;
	const BLUE_BACKGROUND = 44;
	const MAGENTA_BACKGROUND = 45;
	const CYAN_BACKGROUND = 46;
	const LIGHT_GRAY_BACKGROUND = 47;
	const DARK_GRAY_BACKGROUND = 100;
	const LIGHT_RED_BACKGROUND = 101;
	const LIGHT_GREEN_BACKGROUND = 102;
	const LIGHT_YELLOW_BACKGROUND = 103;
	const LIGHT_BLUE_BACKGROUND = 104;
	const LIGHT_MAGENTA_BACKGROUND = 105;
	const LIGHT_CYAN_BACKGROUND = 106;
	const WHITE_BACKGROUND = 107;
	const DEFAULT_BACKGROUND = 49;
	
	// Format
	const BOLD_FORMAT = 1;
	const DIM_FORMAT = 2;
	const UNDERLINED_FORMAT = 4;
	const BLINK_FORMAT = 5;
	const REVERSE_FORMAT = 7;
	const HIDDEN_FORMAT = 8;

	/**
	 * Format string with supplied types
	 */
	public static function Format(string $text, array $types)
	{
		return "\033[" . join(';', $types) . "m$text\033[0m\n";
	}
}
