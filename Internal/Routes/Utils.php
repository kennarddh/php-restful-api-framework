<?php

namespace Internal\Routes;

class Utils
{
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

	public static function GetUrl(): string
	{
		return trim(strtok($_SERVER["REQUEST_URI"], '?'), '/') . '/';
	}
}
