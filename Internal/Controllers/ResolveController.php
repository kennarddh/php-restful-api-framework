<?php

namespace Internal\Controllers;

class ResolveController
{
	public static function ResolveComputed(string $controllerString)
	{
		$name = explode('::', $controllerString)[0];

		$computedName = 'Application\\Controllers\\' . $name;

		return $computedName;
	}

	public static function ResolveFunctionName(string $controllerString)
	{
		return explode('::', $controllerString)[1];
	}
}
