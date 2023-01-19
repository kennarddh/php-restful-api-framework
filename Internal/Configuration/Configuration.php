<?php

namespace Internal\Configuration;

use Dotenv\Dotenv;
use Exception;
use Internal\Logger\Logger;

class Configuration
{
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
