<?php

namespace Application\Routes;

use Internal\Routes\BaseRoutes;

use Internal\Middlewares\Default\Security;
use Application\Middlewares\Auth;

class Routes extends BaseRoutes
{
	public function __construct()
	{
		// Pass true as first argument if this is root route
		parent::__construct(true);

		$this->get('create', 'Home::create');
		$this->post('', Auth::index(), 'Home::post', Auth::after());

		$this->use('users', new UsersRoutes);

		$this->get('file', 'Home::file');

		$this->get('logInfo', 'Home::logInfo');
		$this->get('logError', 'Home::logError');
		$this->get('env', 'Home::env');

		$this->post('validate', 'Home::validate');

		$this->get('mysql', 'Home::mysql');
		$this->post('mysql_insert', 'Home::mysql_insert');
		$this->post('mysql_update', 'Home::mysql_update');
		$this->post('mysql_delete', 'Home::mysql_delete');
		$this->post('mysql_transaction', 'Home::mysql_transaction');

		$this->post('mongo_insert', 'Home::mongo_insert');
		$this->post('mongo_get', 'Home::mongo_get');
		$this->post('mongo_update', 'Home::mongo_update');
		$this->post('mongo_delete', 'Home::mongo_delete');
		$this->post('mongo_transaction', 'Home::mongo_transaction');

		$this->post('db_insert', 'Home::db_insert');
		$this->post('db_get', 'Home::db_get');
		$this->post('db_update', 'Home::db_update');
		$this->post('db_delete', 'Home::db_delete');
		$this->post('db_transaction', 'Home::db_transaction');

		$this->post('jwt_encode', 'Home::jwt_encode');
		$this->post('jwt_decode', 'Home::jwt_decode');

		$this->get('cors', Security::CORS(["http://localhost:3000", "https://localhost:8080"]), 'Home::cors');

		$this->all('all', 'Home::allMethod');
		$this->get('error', 'Home::tryThrow');
		$this->get(':id', 'Home::index');
		$this->all('*', 'Home::matchAll');

		$this->errorHandler('Home::error');
	}
}
