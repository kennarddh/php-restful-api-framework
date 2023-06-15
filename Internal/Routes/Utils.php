<?php

namespace Internal\Routes;

class Utils
{
	/**
	 * Internal use only
	 *
	 * Convert path to regex
	 *
	 * Character * will match anything including / (slash) character
	 */
	public static function ToRegex(string $path, array &$keys)
	{
		$mapPathSplitedRegex = function (string $value, array &$keys2) {
			if ($value === '') return '';

			if ($value[0] === ':') {
				array_push($keys2, substr($value, 1));

				return '([^\/]+)\/';
			}

			if ($value === '*') {
				array_push($keys2, substr($value, 1));

				return '(.+)';
			}

			return preg_quote($value) . '\/';
		};

		$splitted = explode('/', $path);

		$mapped = [];

		foreach ($splitted as $value) {
			array_push($mapped, $mapPathSplitedRegex($value, $keys));
		}

		$regex = join('', $mapped);

		if ($regex === '') {
			$regex = '/^\/$/';
		} else {
			$regex = '/^' . $regex . '/';
		}

		return $regex;
	}

	/**
	 * Get current url
	 */
	public static function GetUrl(): string
	{
		return trim(strtok($_SERVER["REQUEST_URI"], '?'), '/');
	}
}
