<?php

namespace Internal\Routes;

use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Response;
use Application\Routes\Routes;
use Internal\Http\Singleton;
use Internal\Logger\Logger;

/**
 * Main router
 */
class Router
{
	private static function RecursiveMatch(
		string $url,
		string $method,
		BaseRoutes $routes,
		string $tempPath = '',
		array $beforeMiddlewares = [],
		array $afterMiddlewares = [],
	) {
		$keys = [];
		$values = [];

		foreach ($routes->paths as $route) {
			$tempKeys = [];
			$tempValues = [];

			$path = $tempPath === "" ? $route->path : $tempPath . '/' . $route->path;

			Logger::Log("info", 'regex', [
				"path" => $path,
				"keys" => $tempKeys,
				'regex' => Utils::ToRegex($path, $tempKeys)
			]);

			if (
				preg_match(Utils::ToRegex($path, $tempKeys), $url, $tempValues)
			) {
				array_shift($tempKeys);
				array_shift($tempValues);

				$keys = array_merge($keys, $tempKeys);
				$values = array_merge($values, $tempValues);

				if (isset($route->router)) {
					// Partial match

					$beforeMiddlewares = array_merge($beforeMiddlewares, $route->middlewares->before);

					$result = self::RecursiveMatch(
						$url,
						$method,
						$route->router,
						$tempPath . '/' . $route->path,
						$beforeMiddlewares,
						$afterMiddlewares,
					);

					$afterMiddlewares = array_merge($afterMiddlewares, $route->middlewares->after);

					return $result;
				} else {
					if ($method === $route->method || $route->method === 'ALL') {
						$beforeMiddlewares = array_merge($beforeMiddlewares, $route->middlewares->before);
						$afterMiddlewares = array_merge($afterMiddlewares, $route->middlewares->after);

						Logger::Log('info', 'params', ["keys" => $keys, "values" => $values]);

						// Fix params regex matching
						// Fix partial matching auto full match

						// Matched
						return (object) [
							'path' => $tempPath . $route->path,
							'params' => (object) array_combine($keys, $values),
							'controller' => $route->controller,
							'middlewares' => (object) [
								"before" => $beforeMiddlewares,
								"after" => $afterMiddlewares,
							]
						];
					}
				}
			}
		}

		return false;
	}

	/**
	 * Register router
	 */
	public static function Register()
	{
		$url = Utils::GetUrl();
		$method = $_SERVER['REQUEST_METHOD'];

		// Create user defined main route
		$routes = new Routes();

		$data = self::RecursiveMatch($url, $method, $routes);

		if (!$data) {
			// Throw exception if no controller defined for current endpoint
			throw new Exception('No controller for url: ' . $url . ', method: ' . $method);
		}

		// If url match with one of user defined paths in routes call defined controller method

		$request = Singleton::GetRequest()->setParams($data->params);
		$response = Singleton::GetResponse()->setAfterMiddleware(function (Response $response) use ($request, $data) {
			// Run all after middleware sequentially
			foreach ($data->middlewares->after as $middleware) {
				$middleware($request, $response);
			}
		});

		// Run all bedore middleware sequentially
		foreach ($data->middlewares->before as $middleware) {
			$middleware($request, $response);

			// Don't call controller or next before middleware if response ended in current middleware
			if ($response->ended()) {
				return;
			}
		}

		$functionName = ResolveController::ResolveFunctionName($data->controller);

		$controllerName = ResolveController::ResolveComputed($data->controller);

		$controller = new $controllerName($request, $response);

		// Throw error if defined method doesn't exist in controller
		if (!method_exists($controller, $functionName)) {
			throw new Exception("Unable to load method: $functionName in $data->controller");

			return;
		}

		// Call controller method
		$controller->$functionName();
	}
}
