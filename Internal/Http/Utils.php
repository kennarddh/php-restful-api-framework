<?php

namespace Internal\Http;

class Utils
{
	/**
	 * Internal use only
	 * 
	 * Get Body
	 * 
	 * From raw json and form data
	 */
	public static function GetBody(): object
	{
		// Get raw json
		$json = file_get_contents('php://input');

		$data = json_decode($json);

		// Merge $_POST for form data and $data for raw json
		return (object) array_merge((array) $data, $_POST);
	}

	/**
	 * Internal use only
	 * 
	 * Get query parameters
	 */
	public static function GetQueryParameters(): object
	{
		$queries = [];

		parse_str($_SERVER['QUERY_STRING'] ?? '', $queries);

		return (object) $queries;
	}

	/**
	 * Internal use only
	 * 
	 * Get headers
	 */
	public static function GetHeaders(): array
	{
		$headers = [];

		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'HTTP_') === 0) {
				$headers[substr($key, 5)] = $value;
			}
		}

		return $headers;
	}

	/**
	 * Internal use only
	 * 
	 * Check is protocol https
	 */
	public static function IsSecure()
	{
		if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
			return true;
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
			return true;
		} elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
			return true;
		}

		return false;
	}
}
