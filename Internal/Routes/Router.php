<?php

namespace Internal\Routes;

use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Request;
use Internal\Http\Response;
use Internal\Middlewares\ResolveMiddleware;
use Application\Routes\Routes;

class Router
{
	public static function Register()
	{
		$url = Utils::GetUrl();
		$method = $_SERVER['REQUEST_METHOD'];

		$routes = new Routes();

		foreach ($routes->paths as $route) {
			$params = (object) [];

			if (
				Utils::IsUrlMatch($route->path, $url, $params) &&
				($method === $route->method || $route->method === 'ALL')
			) {
				// match

				$request = new Request($params);
				$response = new Response(function (Response $response) use ($request, $route) {
					foreach ($route->middlewares->after as $middleware) {
						$middlewareFunctionName = ResolveMiddleware::ResolveFunctionName($middleware);

						$middlewareName = ResolveMiddleware::ResolveComputed($middleware);

						$middlewareInstance = new $middlewareName($request, $response);

						if (!method_exists($middlewareInstance, $middlewareFunctionName)) {
							throw new Exception("Unable to load method: $middlewareFunctionName in $middleware");

							return;
						}

						$middlewareInstance->$middlewareFunctionName();
					}
				});

				foreach ($route->middlewares->before as $middleware) {
					$middlewareFunctionName = ResolveMiddleware::ResolveFunctionName($middleware);

					$middlewareName = ResolveMiddleware::ResolveComputed($middleware);

					$middlewareInstance = new $middlewareName($request, $response);

					if (!method_exists($middlewareInstance, $middlewareFunctionName)) {
						throw new Exception("Unable to load method: $middlewareFunctionName in $middleware");

						return;
					}

					$middlewareInstance->$middlewareFunctionName();

					if ($response->ended()) {
						return;
					}
				}

				$functionName = ResolveController::ResolveFunctionName($route->controller);

				$controllerName = ResolveController::ResolveComputed($route->controller);

				$controller = new $controllerName($request, $response);

				if (!method_exists($controller, $functionName)) {
					throw new Exception("Unable to load method: $functionName in $route->controller");

					return;
				}

				$controller->$functionName();

				return;
			}
		}

		// 404
		throw new Exception('No controller for url: ' . $url. ', method: ' . $method);
	}
}
