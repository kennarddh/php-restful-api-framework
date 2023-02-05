<?php

namespace Application\Routes;

use Internal\Routes\BaseRoutes;

class Routes extends BaseRoutes
{
	public function __construct()
	{
		// Pass true as first argument if this is root route
		parent::__construct(true);

		$this->get('create', 'Home::create');
		$this->post('', 'Home::post', ["before" => ["Auth::index"], 'after' => ['Auth::after']]);

		$this->use('users', [], new UsersRoutes);

		$this->get('file', 'Home::file');

		$this->get('logInfo', 'Home::logInfo');
		$this->get('logError', 'Home::logError');
		$this->get('env', 'Home::env');

		$this->post('validate', 'Home::validate');
		$this->get('mysql', 'Home::mysql');
		$this->get('mysql_insert', 'Home::mysql_insert');
		$this->get('mysql_update', 'Home::mysql_update');
		$this->get('mysql_delete', 'Home::mysql_delete');

		$this->all('all', 'Home::allMethod');
		$this->get('error', 'Home::tryThrow');
		$this->get(':id', 'Home::index');
		$this->all('*', 'Home::matchAll');

		$this->errorHandler('Home::error');
	}
}
