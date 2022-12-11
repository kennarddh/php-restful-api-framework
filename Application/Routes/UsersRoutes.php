<?php

namespace Application\Routes;

use Internal\Routes\BaseRoutes;

class UsersRoutes extends BaseRoutes
{
	public function __construct()
	{
		parent::__construct();

		$this->get('', 'Home::all');
		$this->group(':id', ["before" => ["Auth::index"]], function (BaseRoutes $routes) {
			$routes->get('balance', 'Home::balance', ['after' => ['Auth::after']]);
		});
	}
}
