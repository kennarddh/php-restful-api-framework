<?php

namespace Internal\Routes;

use Closure;
use Exception;
use Internal\Controllers\ResolveController;
use Internal\Http\Request;
use Internal\Http\Response;
use stdClass;

class BaseRoutes
{
	public array $paths = [];
	public Closure | null $errorHandler = null;

	function __construct()
	{
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

		set_error_handler($errorHandler);
		register_shutdown_function($errorHandler);
	}

	public function request(
		string $path,
		string $method,
		string $controller,
		array|null $middlewares = ['before' => [], 'after' => []]
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

	public function get(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "GET", $controller, $middlewares);
	}

	public function post(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "POST", $controller, $middlewares);
	}

	public function put(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "PUT", $controller, $middlewares);
	}

	public function delete(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "DELETE", $controller, $middlewares);
	}

	public function patch(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "PATCH", $controller, $middlewares);
	}

	public function all(string $path, string $controller, array $middlewares = []): void
	{
		$this->request($path, "ALL", $controller, $middlewares);
	}

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

	public function use(string $path, array $middlewares, BaseRoutes $routes): void
	{
		$this->merge($path, $middlewares, $routes->paths);
	}

	public function group(string $path, array $middlewares, Closure $callback): void
	{
		$newRoutes = new self();

		$callback($newRoutes);

		$this->merge($path, $middlewares, $newRoutes->paths);
	}

	/**
	 * Only response available in error handler
	 */
	public function errorHandler(string $handler): void
	{

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

	public function removeErrorHandler(): void
	{
		$this->errorHandler = null;
	}
}
