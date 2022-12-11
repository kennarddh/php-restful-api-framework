<?php

namespace Internal\Routes;

use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Request;
use Internal\Http\Response;
use Internal\Middlewares\ResolveMiddleware;
use Application\Routes\Routes;

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

		// Create user defined route instance
		$routes = new Routes();

		foreach ($routes->paths as $route) {
			$params = (object) [];

			if (
				Utils::IsUrlMatch($route->path, $url, $params) &&
				($method === $route->method || $route->method === 'ALL')
			) {
				// If url match with one of user defined paths in routes call defined controller method

				$request = new Request($params);
				$response = new Response(function (Response $response) use ($request, $route) {
					// Run all after middleware sequentially
					foreach ($route->middlewares->after as $middleware) {
						$middlewareFunctionName = ResolveMiddleware::ResolveFunctionName($middleware);

						$middlewareName = ResolveMiddleware::ResolveComputed($middleware);

						$middlewareInstance = new $middlewareName($request, $response);

						// Throw error if defined method doesn't exist in middleware 
						if (!method_exists($middlewareInstance, $middlewareFunctionName)) {
							throw new Exception("Unable to load method: $middlewareFunctionName in $middleware");

							return;
						}

						// Call middleware method
						$middlewareInstance->$middlewareFunctionName();
					}
				});

				// Run all after middleware sequentially
				foreach ($route->middlewares->before as $middleware) {
					$middlewareFunctionName = ResolveMiddleware::ResolveFunctionName($middleware);

					$middlewareName = ResolveMiddleware::ResolveComputed($middleware);

					$middlewareInstance = new $middlewareName($request, $response);

					// Throw error if defined method doesn't exist in middleware 
					if (!method_exists($middlewareInstance, $middlewareFunctionName)) {
						throw new Exception("Unable to load method: $middlewareFunctionName in $middleware");

						return;
					}

					// Call middleware method
					$middlewareInstance->$middlewareFunctionName();

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
