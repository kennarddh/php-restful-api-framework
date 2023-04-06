<?php

namespace Internal\Http;

use Closure;

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

	/**
	 * Intenal use only
	 *
	 * Parse range header
	 */
	public static function ParseRangeHeader(string | null $range, int $fileSize, array $options, Closure $endError): array
	{
		$ranges = [];

		if ($range !== null) {
			// Parse range
			$rawRange = trim($range);

			if (!preg_match('/^bytes=((\d+-\d+,?)|(\d+-,?)|(-\d+,?))+$/', $rawRange) || str_ends_with($rawRange, ',')) {
				// Invalid

				($endError)();

				return $ranges;
			}

			[, $rawRanges] = explode('=', $rawRange);

			$rangesArray = explode(',', $rawRanges);

			// Check end is bigger than start
			foreach ($rangesArray as $rangeValue) {
				$rangeStart = 0;
				$rangeEnd = 0;

				$trimmed = trim($rangeValue);

				if (preg_match('/^-\d+$/', $trimmed)) {
					[, $rangeEnd] = explode('-', $trimmed);

					$rangeEnd = (int) $rangeEnd;

					$rangeStart = $fileSize - $rangeEnd;
					$rangeEnd = $fileSize - 1;
				} else if (preg_match('/^\d+-$/', $trimmed)) {
					[$rangeStart] = explode('-', $trimmed);

					$rangeStart = (int) $rangeStart;

					$rangeEnd = $fileSize - 1;
				} else {
					[$rangeStart, $rangeEnd] = explode('-', $trimmed);

					$rangeEnd = (int) $rangeEnd;
					$rangeStart = (int) $rangeStart;
				}

				if (
					$rangeStart > $rangeEnd ||
					$rangeStart < 0 ||
					$rangeEnd < 0 ||
					$rangeStart > $fileSize ||
					$rangeEnd >= $fileSize
				) {
					// Ignore if range is invalid
					if ($options['strict']) {
						($endError)();
					}

					continue;
				}

				array_push($ranges, ['start' => $rangeStart, 'end' => $rangeEnd]);
			}
		} else {
			// Entire document
			$ranges = [];
		}

		return $ranges;
	}
}
