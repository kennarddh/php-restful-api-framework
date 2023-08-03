<?php

namespace Application\Configuration\Logger;

use Internal\Logger\Formatters\StringFormatter;
use Internal\Logger\Logger;
// use Internal\Logger\Transformers\LevelInDataTransformer;
// use Internal\Logger\Transformers\MessageInDataTransformer;
use Internal\Logger\Transports\ConsoleTransport;

class Configuration
{
	public static function Register()
	{
		// Load logger transports
		Logger::AddTransports(
			new ConsoleTransport(
				[
					'acceptLevels' => [
						'error',
						'warning',
						'info'
					],
					'formatters' => [
						new StringFormatter
					],
					'transformers' => [
						// new LevelInDataTransformer,
						// new MessageInDataTransformer
					]
				]
			)
		);
	}
}
