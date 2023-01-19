<?php

namespace Internal\Configuration;

use Dotenv\Dotenv;
use Exception;
use Internal\Logger\Logger;

class Configuration
{
	public static string $HTTPResponseRangeBoundaryString;

	/**
	 * Internal use only
	 *
	 * Load `.env` file
	 */
	public static function LoadEnvFile()
	{
		$dotenv = Dotenv::createImmutable(ENV_DIR);

		try {
			$dotenv->load();
		} catch (Exception) {
			Logger::Log('warning', 'Env file doesn\'t exist');
		}

		self::DefaultENV();
	}

	/**
	 * Internal use only
	 *
	 * Default env
	 */
	static public function DefaultENV()
	{
		self::$HTTPResponseRangeBoundaryString = self::getEnv('HTTP_RESPONSE_RANGE_BOUNDARY_STRING') ?? '3d6b6a416f9b5';
	}

	/**
	 * Get env
	 *
	 * If env doesn't exist it return null
	 */
	public static function getEnv(string $key)
	{
		if (isset($_ENV[$key])) return $_ENV[$key];

		return null;
	}
}
