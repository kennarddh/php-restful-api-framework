<?php

namespace Application\Configuration;

enum EnvironmentType
{
	case Development;
	case Production;
}

class Environment
{
	public static EnvironmentType $environmentType = EnvironmentType::Development;
	public static bool $displayError = false;

	public static function Register()
	{
		self::$displayError = self::$environmentType == EnvironmentType::Development;
	}
}
