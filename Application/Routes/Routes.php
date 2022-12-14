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

		$this->all('all', 'Home::allMethod');
		$this->get('error', 'Home::tryThrow');
		$this->get(':id', 'Home::index');
		$this->all('*', 'Home::matchAll');

		$this->errorHandler('Home::error');
	}
}
