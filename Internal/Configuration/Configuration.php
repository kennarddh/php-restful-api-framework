<?php

namespace Internal\Configuration;

use Dotenv\Dotenv;
use Exception;
use Internal\Logger\Logger;

class Configuration
{
	public static function LoadEnvFile()
	{
		$dotenv = Dotenv::createImmutable(ENV_DIR);

		try {
			$dotenv->load();
		} catch (Exception) {
			Logger::Log('warning', 'Env file doesn\'t exist');
		}
	}
}
