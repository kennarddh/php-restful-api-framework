<?php

namespace Application\Routes;

use Internal\Routes\BaseRoutes;
use Application\Middlewares\Auth;

class UsersRoutes extends BaseRoutes
{
	public function __construct()
	{
		parent::__construct();

		$this->get('', 'Home::all');
		$this->group(':id', function (BaseRoutes $routes) {
			$routes->setBeforeMiddlewares(Auth::index());

			$routes->get('balance', 'Home::balance', Auth::after());
		});
	}
}
