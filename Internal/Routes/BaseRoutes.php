<?php

namespace Internal\Routes;

use Closure;
use Common\OutputBuffer;
use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Singleton;
use Internal\Logger\Logger;

/**
 * All routes must extend BaseRoutes
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

	/**
	 * Internal use only
	 *
	 * Is root routes
	 *
	 * Only 1 root route
	 */
	protected bool $isRoot = false;

	function __construct(bool $isRoot = false)
	{
		$this->isRoot = $isRoot;

		if (!$this->isRoot) {
			// Not root route

			return;
		}

		// Error handler wrapper
		$errorHandler = function () {
			if ($this->errorHandler === null) return false;

			$error = error_get_last();

			if ($error === null) {
				OutputBuffer::flush();

				return;
			}

			$type = $error['type'];
			$message = $error['message'];
			$file = $error['file'];
			$line = $error['line'];

			// Set end to false
			Singleton::GetResponse()->cancelEnd();

			// Clear body
			OutputBuffer::clear();

			Logger::Log('error', 'Error Occured', ["type" => $type, "message" => $message, "file" => $file, "line" => $line]);

			($this->errorHandler)(
				$type,
				$message,
				$file,
				$line,
			);

			OutputBuffer::flush();

			return true;
		};

		// Register shutdown function
		register_shutdown_function($errorHandler);
	}

	/**
	 * Add endpoint
	 *
	 * Character * will accept anything including / (slash) character
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
	 * Character * will accept anything including / (slash) character
	 */
	public function get(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "GET", $controller, $middlewares);
	}

	/**
	 * Add post method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function post(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "POST", $controller, $middlewares);
	}

	/**
	 * Add put method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function put(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "PUT", $controller, $middlewares);
	}

	/**
	 * Add delete method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function delete(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "DELETE", $controller, $middlewares);
	}

	/**
	 * Add patch method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function patch(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "PATCH", $controller, $middlewares);
	}

	/**
	 * Add all method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function all(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "ALL", $controller, $middlewares);
	}

	/**
	 * Internal use only
	 *
	 * Merge current route with other route route
	 */
	protected function merge(string $path, array $middlewares, BaseRoutes $route)
	{
		if ($route->isRoot && $this->isRoot) {
			throw new Exception('Cannot merge 2 root routes');

			return;
		}

		if ($middlewares === null) $middlewares = [];

		if (!isset($middlewares['after'])) $middlewares['after'] = [];
		if (!isset($middlewares['before'])) $middlewares['before'] = [];

		$paths = $route->paths;

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
	public function use(string $path, BaseRoutes $routes, array $middlewares): void
	{
		$this->merge($path, $middlewares, $routes);
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

		$this->merge($path, $middlewares, $newRoutes);
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
		if (!$this->isRoot) {
			throw new Exception('Add error handler only can be called from main route');

			return;
		}

		if ($this->errorHandler !== null) {
			throw new Exception('Error handler already set');

			return;
		}

		$functionName = ResolveController::ResolveFunctionName($handler);

		$handlerName = ResolveController::ResolveComputed($handler);

		$request = Singleton::GetRequest();
		$response = Singleton::GetResponse();

		$handlerInstance = new $handlerName($request, $response);

		if (!method_exists($handlerInstance, $functionName)) {
			throw new Exception("Unable to load method: $functionName in $handler");

			return;
		}

		$this->errorHandler = function (
			$type,
			$message,
			$file,
			$line,
		) use ($handlerInstance, $functionName) {
			$handlerInstance->$functionName(
				$type,
				$message,
				$file,
				$line,
			);
		};
	}

	/**
	 * Remove current error handler
	 */
	public function removeErrorHandler(): void
	{
		if (!$this->isRoot) {
			throw new Exception('Remove error handler only can be called from main route');

			return;
		}

		$this->errorHandler = null;
	}
}
