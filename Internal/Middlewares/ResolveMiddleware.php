<?php

namespace Internal\Middlewares;

class ResolveMiddleware
{
	/**
	 * Internal use only
	 * 
	 * Resolve middleware class
	 */
	public static function ResolveComputed(string $controllerString)
	{
		$name = explode('::', $controllerString)[0];

		$computedName = 'Application\\Middlewares\\' . $name;

		return $computedName;
	}

	/**
	 * Internal use only
	 * 
	 * Resolve middleware method to call
	 */
	public static function ResolveFunctionName(string $controllerString)
	{
		return explode('::', $controllerString)[1];
	}
}
