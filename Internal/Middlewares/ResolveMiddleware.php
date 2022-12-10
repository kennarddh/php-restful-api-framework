<?php

namespace Internal\Middlewares;

class ResolveMiddleware
{
	public static function ResolveComputed(string $controllerString)
	{
		$name = explode('::', $controllerString)[0];

		$computedName = 'Middlewares\\' . $name;

		return $computedName;
	}

	public static function ResolveFunctionName(string $controllerString)
	{
		return explode('::', $controllerString)[1];
	}
}
