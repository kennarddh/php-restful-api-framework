<?php

namespace Scripts\Commands\Start;

use Scripts\Console\TextFormatter;
use Scripts\Commands\BaseCommand;
use Scripts\Commands\Traits\ExecuteTrait;
use Scripts\Console\ConsoleSingleton;

class Start extends BaseCommand
{
	use ExecuteTrait;

	/**
	 * Command name
	 */
	public static string $name = 'start';

	/**
	 * Command description
	 */
	public static string $description = 'Start development server';

	/**
	 * Arguments for help command
	 */
	public static $arguments = [
		'--php'  => 'PHP binary (default: "PHP_BINARY")',
		'--host' => 'HTTP host (default: "localhost")',
		'--port' => 'HTTP port (default: "8080")',
	];

	public static function execute()
	{
		$console = ConsoleSingleton::GetConsole();

		// Get data from arguments
		$phpBinary = escapeshellarg($console->arguments->namedArguments['php'] ?? PHP_BINARY);
		$host = $console->arguments->namedArguments['host'] ?? 'localhost';
		$port = (int) ($console->arguments->namedArguments['port'] ?? '8080');

		// Path to public folder
		$publicFolder = escapeshellarg(PATH_FROM_INDEX_TO_APPLICATION . 'Public' . DIRECTORY_SEPARATOR);

		// Mimic Apache's mod_rewrite functionality
		$router = escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'Router.php');

		$console->write('Development server started on http://' . $host . ':' . $port, [TextFormatter::GREEN_FOREGROUND]);

		$console->writeEmptyLine();

		// Escape host
		$host = escapeshellarg($host);

		// Run php default webserver
		passthru("$phpBinary -q -S $host:$port -t $publicFolder $router");
	}
}
