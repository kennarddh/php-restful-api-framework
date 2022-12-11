<?php

namespace Common;

class AutoLoader
{
	/**
	 * Register autoloader
	 */
	public static function Register()
	{
		spl_autoload_register(function ($class) {
			$file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

			$file = PATH_FROM_INDEX_TO_APPLICATION . $file;

			if (file_exists($file)) {
				require $file;

				return true;
			}

			return false;
		});
	}
}
