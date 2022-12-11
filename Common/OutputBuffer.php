<?php

namespace Common;

class OutputBuffer
{
	protected static string $buffer = '';

	/**
	 * Flush buffer to output
	 */
	public static function flush()
	{
		echo self::$buffer;

		self::clear();
	}

	/**
	 * Empty buffer
	 */
	public static function clear()
	{
		self::$buffer = '';
	}

	/**
	 * Set buffer
	 */
	public static function set(string $str)
	{
		self::$buffer = $str;
	}

	/**
	 * Add buffer
	 */
	public static function add(string $str)
	{
		self::$buffer .= $str;
	}
}
