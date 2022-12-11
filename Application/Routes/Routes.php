<?php

namespace Application\Routes;

use Internal\Routes\BaseRoutes;

class Routes extends BaseRoutes
{
	public function __construct()
	{
		parent::__construct();

		$this->get('create', 'Home::create');
		$this->post('', 'Home::post', ["before" => ["Auth::index"], 'after' => ['Auth::after']]);

		$this->use('users', [], new UsersRoutes);

		$this->all('all', 'Home::allMethod');
		$this->get('error', 'Home::tryThrow');
		$this->get(':id', 'Home::index');
		$this->get('*', 'Home::matchAll');

		$this->errorHandler('Home::error');
	}
}
