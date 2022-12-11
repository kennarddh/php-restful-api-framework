<?php

namespace Scripts\Commands\Start;

use Scripts\CLI\TextFormatter;
use Scripts\Commands\BaseCommand;

class Start extends BaseCommand
{
	/**
	 * Command name
	 */

	public static string $name = 'start';

	/**
	 * Command description
	 */
	public static string $description = 'Start development server';


	protected $arguments = [
		'--php'  => 'PHP binary (default: "PHP_BINARY")',
		'--host' => 'HTTP host (default: "localhost")',
		'--port' => 'HTTP port (default: "8080")',
	];

	/** 
	 * Execute
	 */
	public function execute()
	{
		$phpBinary = escapeshellarg($this->cli->arguments->namedArguments['php'] ?? PHP_BINARY);
		$host = $this->cli->arguments->namedArguments['host'] ?? 'localhost';
		$port = (int) ($this->cli->arguments->namedArguments['port'] ?? '8080');

		$publicFolder = escapeshellarg(PATH_FROM_INDEX_TO_APPLICATION . 'Public' . DIRECTORY_SEPARATOR);

		// Mimic Apache's mod_rewrite functionality
		$router = escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . 'Router.php');

		$this->cli->writeLine('Development server started on http://' . $host . ':' . $port, [TextFormatter::$GREEN_FOREGROUND]);

		$host = escapeshellarg($host);

		passthru("$phpBinary -S $host:$port -t $publicFolder $router");
	}
}
