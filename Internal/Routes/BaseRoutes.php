<?php

namespace Internal\Routes;

use Closure;
use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Request;
use Internal\Http\Response;
use stdClass;

/**
 * All routes must to extend BaseRoutes
 */
class BaseRoutes
{
	/**
	 * Internal use only
	 * 
	 * All paths
	 */
	public array $paths = [];

	/**
	 * Internal use only
	 * 
	 * Current error handler
	 * 
	 * Can only be set in root route
	 * 
	 * If set in other routes error handler will be ignored
	 */
	public Closure | null $errorHandler = null;

	function __construct()
	{
		// Error handler wrapper
		$errorHandler = function () {
			if ($this->errorHandler === null) return false;

			$error = error_get_last();

			if ($error === null) return;

			$type = $error['type'];
			$message = $error['message'];
			$file = $error['file'];
			$line = $error['line'];

			($this->errorHandler)(
				$type,
				$message,
				$file,
				$line,
			);

			return true;
		};

		// Register shutdown function
		register_shutdown_function($errorHandler);
	}

	/**
	 * Add endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function request(
		string $path,
		string $method,
		string $controller,
		array | null $middlewares = ['before' => [], 'after' => []]
	): void {
		if (!isset($middlewares['after'])) $middlewares['after'] = [];
		if (!isset($middlewares['before'])) $middlewares['before'] = [];

		array_push($this->paths, (object) [
			'path' => $path,
			'method' => $method,
			'controller' => $controller,
			'middlewares' => (object) $middlewares
		]);
	}

	/**
	 * Add get method endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function get(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "GET", $controller, $middlewares);
	}

	/**
	 * Add post method endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function post(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "POST", $controller, $middlewares);
	}

	/**
	 * Add put method endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function put(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "PUT", $controller, $middlewares);
	}

	/**
	 * Add delete method endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function delete(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "DELETE", $controller, $middlewares);
	}

	/**
	 * Add patch method endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function patch(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "PATCH", $controller, $middlewares);
	}

	/**
	 * Add all method endpoint
	 * 
	 * character * will accept anything including / (slash) character
	 */
	public function all(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "ALL", $controller, $middlewares);
	}

	/**
	 * Internal use only
	 * 
	 * Merge 2 paths
	 */
	protected function merge(string $path, array $middlewares, array $paths)
	{
		if ($middlewares === null) $middlewares = [];

		if (!isset($middlewares['after'])) $middlewares['after'] = [];
		if (!isset($middlewares['before'])) $middlewares['before'] = [];

		foreach ($paths as $iterPath) {
			array_push($this->paths, (object) [
				'path' => $path . '/' . $iterPath->path,
				'method' => $iterPath->method,
				'controller' => $iterPath->controller,
				'middlewares' => (object) [
					'before' => array_merge($middlewares['before'], $iterPath->middlewares->before),
					'after' => array_merge($middlewares['after'], $iterPath->middlewares->after),
				]
			]);
		}
	}

	/**
	 * Merge with other routes instance
	 */
	public function use(string $path, array $middlewares, BaseRoutes $routes): void
	{
		$this->merge($path, $middlewares, $routes->paths);
	}

	/**
	 * Group endpoint
	 * 
	 * Group can be nested
	 * 
	 * @param Closure $callback Callback will called with $routes parameter. To add routes that will have every group property add endpoint in $routes parameter
	 */
	public function group(string $path, array $middlewares, Closure $callback): void
	{
		$newRoutes = new self();

		$callback($newRoutes);

		$this->merge($path, $middlewares, $newRoutes->paths);
	}

	/**
	 * Register error handler
	 * 
	 * Only one error handler can be registed
	 * 
	 * To change error handler first remove previous error handler by using removeErrorHandler method
	 * 
	 * Can only be set in root route
	 * 
	 * If set in other routes error handler will be ignored
	 * 
	 * Only response available in error handler
	 */
	public function errorHandler(string $handler): void
	{
		if ($this->errorHandler !== null) {
			throw new Exception('Error handler already set');

			return;
		}

		$functionName = ResolveController::ResolveFunctionName($handler);

		$handlerName = ResolveController::ResolveComputed($handler);

		$request = new Request(new stdClass);
		$response = new Response(function () {
		});

		$handlerInstance = new $handlerName($request, $response);

		if (!method_exists($handlerInstance, $functionName)) {
			throw new Exception("Unable to load method: $functionName in $handler");

			return;
		}

		$this->errorHandler = function () use ($handlerInstance, $functionName) {
			$handlerInstance->$functionName();
		};
	}

	/**
	 * Remove current error handler
	 */
	public function removeErrorHandler(): void
	{
		$this->errorHandler = null;
	}
}
