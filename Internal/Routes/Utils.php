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
			$regex = '/^' . $regex . '$/';
		}

		return $regex;
	}

	/**
	 * Check is path match with supplied url
	 * 
	 * Return path parameters in refrence parameter
	 */
	public static function IsUrlMatch(string $path, string $url, object &$params)
	{
		$values = [];
		$keys = [];

		$result = preg_match(Utils::ToRegex($path, $keys), $url, $values);

		if ($result) {
			// Remove first element which is path
			array_shift($values);

			$params = (object) array_combine($keys, $values);
		} else {
			$params = (object) [];
		}

		return $result;
	}

	/**
	 * Get current url
	 */
	public static function GetUrl(): string
	{
		return trim(strtok($_SERVER["REQUEST_URI"], '?'), '/') . '/';
	}
}
