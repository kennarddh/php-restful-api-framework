<?php

namespace Internal\Controllers;

class ResolveController
{
	/**
	 * Resolve controller namespace
	 */
	public static function ResolveComputed(string $controllerString)
	{
		$name = explode('::', $controllerString)[0];

		$computedName = 'Application\\Controllers\\' . $name;

		return $computedName;
	}

	/**
	 * Resolve controller method to call
	 */
	public static function ResolveFunctionName(string $controllerString)
	{
		return explode('::', $controllerString)[1];
	}
}
