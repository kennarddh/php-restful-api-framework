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
	private static function RecursiveMatch(
		string $url,
		string $method,
		BaseRoutes $routes,
		array $unmatchedUrl = null,
		array $middlewares = ["after" => [], "before" => []],
		array $params = []
	) {
		if ($unmatchedUrl === null) {
			$unmatchedUrl = explode('/', $url);
		}

		foreach ($routes->paths as $path) {
			$pathSliced = explode('/', $path->path);

			if (count($pathSliced) > count($unmatchedUrl)) continue;

			$match = true;

			$unmatchedUrlCopy = $unmatchedUrl;

			for ($i = 0; $i < count($pathSliced); $i++) {
				array_shift($unmatchedUrlCopy);

				if (
					$pathSliced[$i] !== $unmatchedUrl[$i] &&
					!str_starts_with($pathSliced[$i], ':')
				) {
					$match = false;

					break;
				}

				if (str_starts_with($pathSliced[$i], ':')) {
					$params[substr($pathSliced[$i], 1)] = $unmatchedUrl[$i];
				}
			}

			if ($match) {
				array_push($middlewares['before'], ...$routes->beforeMiddlewares, ...$path->middlewares->before);
				array_push($middlewares['after'], ...$routes->afterMiddlewares, ...$path->middlewares->after);

				if (
					isset($path->controller) &&
					($path->method === $method || $path->method === 'ALL')
				) {
					// End

					return [
						"controller" => $path->controller,
						"middlewares" => [
							"after" => array_reverse($middlewares['after']),
							"before" => $middlewares['before'],
						],
						"params" => $params,
					];
				} else if (!isset($path->controller)) {
					// Partial

					return self::RecursiveMatch(
						$url,
						$method,
						$path->router,
						$unmatchedUrlCopy,
						$middlewares,
						$params
					);
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

		$request = Singleton::GetRequest()->setParams((object) $data['params']);
		$response = Singleton::GetResponse()->setAfterMiddleware(function (Response $response) use ($request, $data) {
			// Run all after middleware sequentially
			foreach ($data['middlewares']['after'] as $middleware) {
				$middleware($request, $response);
			}
		});

		// Run all before middleware sequentially
		foreach ($data['middlewares']['before'] as $middleware) {
			$middleware($request, $response);

			// Don't call controller or next before middleware if response ended in current middleware
			if ($response->ended()) {
				return;
			}
		}

		$functionName = ResolveController::ResolveFunctionName($data['controller']);

		$controllerName = ResolveController::ResolveComputed($data['controller']);

		$controller = new $controllerName($request, $response);

		// Throw error if defined method doesn't exist in controller
		if (!method_exists($controller, $functionName)) {
			throw new Exception("Unable to load method: $functionName in " . $data['controller']);

			return;
		}

		// Call controller method
		$controller->$functionName();
	}
}
