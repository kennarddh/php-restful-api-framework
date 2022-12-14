<?php

namespace Internal\Http;

use stdClass;

/**
 * Internal use only
 * 
 * HTTP singleton
 */
class Singleton
{
	private static Request $request;
	private static Response $response;

	/**
	 * Internal use only
	 * 
	 * Get request
	 */
	public static function GetRequest(): Request
	{
		if (!isset(self::$request)) {
			self::$request = new Request(new stdClass);
		}

		return self::$request;
	}

	/**
	 * Internal use only
	 * 
	 * Get response
	 */
	public static function GetResponse(): Response
	{
		if (!isset(self::$response)) {
			self::$response = new Response();
		}

		return self::$response;
	}
}
