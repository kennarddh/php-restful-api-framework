<?php

namespace Internal\Controllers;

class ResolveController
{
	/**
	 * Internal use only
	 * 
	 * Resolve controller class
	 */
	public static function ResolveComputed(string $controllerString)
	{
		$name = explode('::', $controllerString)[0];

		$computedName = 'Application\\Controllers\\' . $name;

		return $computedName;
	}

	/**
	 * Internal use only
	 * 
	 * Resolve controller method to call
	 */
	public static function ResolveFunctionName(string $controllerString)
	{
		return explode('::', $controllerString)[1];
	}
}
