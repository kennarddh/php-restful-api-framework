<?php

namespace Application\Configuration\Database;

use Internal\Database\Adapters\MongoDBAdapter;
use Internal\Database\Database;

class Configuration
{
	public static function Register()
	{
		$adapter = new MongoDBAdapter([
			'uri' => 'mongodb://127.0.0.1:27017/?replicaSet=rs0',
			'database' => 'test_api_framework',
		]);

		Database::SetAdapter($adapter);
	}
}
