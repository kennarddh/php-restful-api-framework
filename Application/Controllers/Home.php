<?php

namespace Application\Controllers;

use Exception;
use Internal\Configuration\Configuration;
use Internal\Controllers\BaseController;
use Internal\Database\Adapters\MongoDBAdapter;
use Internal\Database\Adapters\MySqlAdapter;
use Internal\Database\Database;
use Internal\Libraries\Validation;
use Internal\Logger\Logger;
use MongoDB\BSON;
use Internal\Libraries\JWT;

final class Home extends BaseController
{
	public function index()
	{
		$this->response->send([
			"params" => $this->request->params,
		]);
	}

	public function create()
	{
		$this->response->send([
			'token' => $this->request->header('Authorization')
		], 201);
	}

	public function post()
	{
		$this->response->send([
			'body' => $this->request->body,
			'queryParameters' => $this->request->queryParameters,
			'tokenHeader' => $this->request->data['token'],
			'baseUrl' => $this->request->baseUrl,
			'ip' => $this->request->ip
		], 200);
	}

	public function all()
	{
		$this->response->send([
			'message' => 'all',
		], 200);
	}

	public function balance()
	{
		$this->response->send([
			'params' => $this->request->params
		], 200);
	}

	public function allMethod()
	{
		$this->response->send([
			'message' => 'all method',
			'method' => $this->request->method
		]);
	}

	public function matchAll()
	{
		$this->response->send([
			'baseUrl' => $this->request->baseUrl,
			'method' => $this->request->method
		], 200);
	}

	public function error()
	{
		$this->response->send([
			'error' => 'Internal Server Error',
		], 500);
	}

	public function tryThrow()
	{
		$this->response->send([
			'error' => 'Error',
		], 200);

		throw new Exception('error');
	}

	public function file()
	{
		$filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Public' . DIRECTORY_SEPARATOR . 'DownloadImage.png';

		$this->response->sendFile('DownloadImage.png', $filePath, true);
	}

	public function logInfo()
	{
		Logger::Log('info', 'Test info log', ['data' => 'test']);
	}

	public function logError()
	{
		Logger::Log('error', 'Test error log');
	}

	public function validate()
	{
		$validation = new Validation($this->request->body, [
			"age" => ["IsSet", "Bail", "IsNumber"],
			"name" => ["IsSet", "Bail", "NotNull"],
			"obj" => ["IsSet", "Bail", "NotNull"],
			"obj.id" => ["IsSet", "Bail", "NotNull", "Bail", "IsNumber"]
		]);

		[$isValid, $errors] = $validation->validate();

		$this->response->send([
			'isValid' => $isValid,
			'errors' => $errors
		], 200);
	}

	public function env()
	{
		$this->response->send(["data" => Configuration::getEnv('ABC')], 200);
	}

	public function mysql()
	{
		$db = new MySqlAdapter([
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'database' => 'test_api_framework',
			'port' => 3306
		]);

		$this->response->send($db->Get('test', ['id', 'name'], ['name' => 'x']), 200);
	}

	public function mysql_insert()
	{
		$db = new MySqlAdapter([
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'database' => 'test_api_framework',
			'port' => 3306
		]);

		$this->response->send(['result' => $db->Insert('test', ['name' => 'x'])], 200);
	}

	public function mysql_update()
	{
		$db = new MySqlAdapter([
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'database' => 'test_api_framework',
			'port' => 3306
		]);

		$this->response->send(['result' => $db->Update('test', ['name' => 'c'], ['name' => 'x'])], 200);
	}

	public function mysql_delete()
	{
		$db = new MySqlAdapter([
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'database' => 'test_api_framework',
			'port' => 3306
		]);

		$this->response->send(['result' => $db->Delete('test', ['name' => 'x'])], 200);
	}

	public function mysql_transaction()
	{
		$db = new MySqlAdapter([
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'database' => 'test_api_framework',
			'port' => 3306
		]);

		try {

			$db->Transaction(function ($session) use ($db) {
				$db->Insert('test', ['name' => 'baryzxc'], ['session' => $session]);
				$db->Insert('test', ['nothing' => 'asd'], ['session' => $session]);
			});
			$this->response->send(['result' => 'success'], 200);
		} catch (Exception $error) {
			$this->response->send(['result' => 'error transaction failed'], 500);
		}
	}

	public function mongo_insert()
	{
		$db = new MongoDBAdapter([
			'uri' => 'mongodb://127.0.0.1:27017/?replicaSet=rs0',
			'database' => 'test_api_framework',
		]);

		$this->response->send(['result' => $db->Insert('test', ['name' => 'x'])], 200);
	}

	public function mongo_get()
	{
		$db = new MongoDBAdapter([
			'uri' => 'mongodb://127.0.0.1:27017/?replicaSet=rs0',
			'database' => 'test_api_framework',
		]);

		$this->response->send(['result' => $db->Get('test', ['name', '_id'], ['_id' => new BSON\ObjectID('63e4dc0c90c62b80d70f0e56')])], 200);
	}

	public function mongo_update()
	{
		$db = new MongoDBAdapter([
			'uri' => 'mongodb://127.0.0.1:27017/?replicaSet=rs0',
			'database' => 'test_api_framework',
		]);

		$this->response->send(['result' => $db->Update('test', ['name' => 'foo'], ['name' => 'x'])], 200);
	}

	public function mongo_delete()
	{
		$db = new MongoDBAdapter([
			'uri' => 'mongodb://127.0.0.1:27017/?replicaSet=rs0',
			'database' => 'test_api_framework',
		]);

		$this->response->send(['result' => $db->Delete('test', ['_id' => 'a'])], 200);
	}

	public function mongo_transaction()
	{
		$db = new MongoDBAdapter([
			'uri' => 'mongodb://127.0.0.1:27017/?replicaSet=rs0',
			'database' => 'test_api_framework',
		]);

		try {
			$db->Transaction(function ($session) use ($db) {
				$db->Insert('test', ['nothing' => 'asadghfshfssd'], ['session' => $session]);
				throw new Exception();
			});

			$this->response->send(['result' => 'success'], 200);
		} catch (Exception $error) {
			$this->response->send(['result' => 'error transaction failed'], 500);
		}
	}

	public function db_insert()
	{
		$this->response->send(['result' => Database::Insert('test', ['name' => 'x'])], 200);
	}

	public function db_get()
	{
		$this->response->send(['result' => Database::Get('test', ['name', '_id'], ['_id' => new BSON\ObjectID('64c2557f0c7d946d430cde64')])], 200);
	}

	public function db_update()
	{
		$this->response->send(['result' => Database::Update('test', ['name' => 'foo'], ['name' => 'x'])], 200);
	}

	public function db_delete()
	{
		$this->response->send(['result' => Database::Delete('test',['_id' => new BSON\ObjectID('64c2557f0c7d946d430cde64')])], 200);
	}

	public function db_transaction()
	{
		try {
			Database::Transaction(function ($session) {
				Database::Insert('test', ['nothing2' => 'asadghfshfssd'], ['session' => $session]);
				throw new Exception();
			});

			$this->response->send(['result' => 'success'], 200);
		} catch (Exception $error) {
			$this->response->send(['result' => 'error transaction failed'], 500);
		}
	}

	public function jwt_decode()
	{
		$this->response->send(['result' => JWT::Decode(
			"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmb28iOiJiYXIiLCJ4IjpbInkiLCJ6Il19.RPHKf4vAdRYXrqgi-ecuNY5ODUjPJ682NNiPQ66vsX8",
			'test',
			'HS256'
		)], 200);
	}

	public function jwt_encode()
	{
		$this->response->send(['result' => JWT::Encode(
			[
				'foo' => 'bar',
				'x' => [
					'y',
					'z'
				]
			],
			'test',
			'HS256'
		)], 200);
	}

	public function cors()
	{
		$this->response->send(['result' => 'success'], 200);
	}
}
