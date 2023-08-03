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

	/**
	 * Internal use only
	 *
	 * After middlewares list
	 */
	public array $afterMiddlewares = [];

	/**
	 * Internal use only
	 *
	 * Before middlewares list
	 */
	public array $beforeMiddlewares = [];

	function __construct(bool $isRoot = false)
	{
		$this->isRoot = $isRoot;

		if (!$this->isRoot) {
			// Not root route

			return;
		}

		// Error handler wrapper
		$errorHandler = function () {
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

			if ($this->errorHandler !== null) {
				($this->errorHandler)(
					$type,
					$message,
					$file,
					$line,
				);
			}

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
		mixed ...$controllerAndMiddlewares
	): void {
		$middlewares = ["after" => [], "before" => []];
		$controller = null;

		foreach ($controllerAndMiddlewares as $controllerOrMiddleware) {
			if (gettype($controllerOrMiddleware) === "string") {
				$controller = $controllerOrMiddleware;
			} else {
				if ($controller === null) {
					$middlewares["before"][] = $controllerOrMiddleware;
				} else {
					$middlewares["after"][] = $controllerOrMiddleware;
				}
			}
		}

		if ($controller === null) {
			throw new Exception("No controller found");
		}

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
	public function get(
		string $path,
		mixed ...$controllerAndMiddlewares
	): void {
		$this->request($path, "GET", ...$controllerAndMiddlewares);
	}

	/**
	 * Add post method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function post(
		string $path,
		mixed ...$controllerAndMiddlewares
	): void {
		$this->request($path, "POST", ...$controllerAndMiddlewares);
	}

	/**
	 * Add put method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function put(
		string $path,
		mixed ...$controllerAndMiddlewares
	): void {
		$this->request($path, "PUT", ...$controllerAndMiddlewares);
	}

	/**
	 * Add delete method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function delete(
		string $path,
		mixed ...$controllerAndMiddlewares
	): void {
		$this->request($path, "DELETE", ...$controllerAndMiddlewares);
	}

	/**
	 * Add patch method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function patch(
		string $path,
		mixed ...$controllerAndMiddlewares
	): void {
		$this->request($path, "PATCH", ...$controllerAndMiddlewares);
	}

	/**
	 * Add all method endpoint
	 *
	 * Character * will accept anything including / (slash) character
	 */
	public function all(
		string $path,
		mixed ...$controllerAndMiddlewares
	): void {
		$this->request($path, "ALL", ...$controllerAndMiddlewares);
	}

	/**
	 * Internal use only
	 *
	 * Merge current route with other route route
	 */
	protected function merge(
		string $path,
		mixed ...$routerAndMiddlewares
	) {
		$middlewares = ["after" => [], "before" => []];
		$router = null;

		foreach ($routerAndMiddlewares as $routerOrMiddleware) {
			if ($routerOrMiddleware instanceof BaseRoutes) {
				$router = $routerOrMiddleware;
			} else {
				if ($router === null) {
					$middlewares["before"][] = $routerOrMiddleware;
				} else {
					$middlewares["after"][] = $routerOrMiddleware;
				}
			}
		}

		if (!($router instanceof BaseRoutes)) {
			throw new Exception("No router found");
		}

		if ($router->isRoot && $this->isRoot) {
			throw new Exception('Cannot merge 2 root routes');

			return;
		}

		array_push($this->paths, (object) [
			'path' => $path,
			'middlewares' => (object) $middlewares,
			'router' => $router,
		]);
	}

	/**
	 * Merge with other routes instance
	 */
	public function use(
		string $path,
		mixed ...$routerAndMiddlewares
	): void {
		$this->merge($path, ...$routerAndMiddlewares);
	}

	/**
	 * Group endpoint
	 *
	 * Group can be nested
	 *
	 * @param Closure $callback Callback will called with $routes parameter. To add routes that will have every group property add endpoint in $routes parameter
	 */
	public function group(string $path, Closure $callback): void
	{
		$newRoutes = new self();

		$callback($newRoutes);

		$this->use($path, $newRoutes);
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

	/**
	 * Set after middleware
	 */
	public function setAfterMiddlewares(Closure ...$middlewares)
	{
		$this->afterMiddlewares = array_merge($this->afterMiddlewares, $middlewares);
	}

	/**
	 * Set before middleware
	 */
	public function setBeforeMiddlewares(Closure ...$middlewares)
	{
		$this->beforeMiddlewares = array_merge($this->beforeMiddlewares, $middlewares);
	}
}
