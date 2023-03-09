<?php

namespace Internal\Routes;

use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Response;
use Application\Routes\Routes;
use Internal\Http\Singleton;

/**
 * Main router
 */
class Router
{
	/**
	 * Register router
	 */
	public static function Register()
	{
		$url = Utils::GetUrl();
		$method = $_SERVER['REQUEST_METHOD'];

		// Create user defined main route
		$routes = new Routes();

		foreach ($routes->paths as $route) {
			$params = (object) [];

			if (
				Utils::IsUrlMatch($route->path, $url, $params) &&
				($method === $route->method || $route->method === 'ALL')
			) {
				// If url match with one of user defined paths in routes call defined controller method

				$request = Singleton::GetRequest()->setParams($params);
				$response = Singleton::GetResponse()->setAfterMiddleware(function (Response $response) use ($request, $route) {
					// Run all after middleware sequentially
					foreach ($route->middlewares->after as $middleware) {
						$middleware($request, $response);
					}
				});

				// Run all bedore middleware sequentially
				foreach ($route->middlewares->before as $middleware) {
					$middleware($request, $response);

					// Don't call controller or next before middleware if response ended in current middleware
					if ($response->ended()) {
						return;
					}
				}

				$functionName = ResolveController::ResolveFunctionName($route->controller);

				$controllerName = ResolveController::ResolveComputed($route->controller);

				$controller = new $controllerName($request, $response);

				// Throw error if defined method doesn't exist in controller
				if (!method_exists($controller, $functionName)) {
					throw new Exception("Unable to load method: $functionName in $route->controller");

					return;
				}

				// Call controller method
				$controller->$functionName();

				return;
			}
		}

		// Throw exception if no controller defined for current endpoint
		throw new Exception('No controller for url: ' . $url . ', method: ' . $method);
	}
}
