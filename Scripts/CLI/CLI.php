<?php

namespace Scripts\CLI;

class CLI
{
	protected $output;
	protected $error;

	function __construct($output = STDOUT, $error = STDERR)
	{
		$this->output = $output;
		$this->error = $error;
	}

	public function write(string $text)
	{
		fwrite($this->output, $text);
	}

	public function writeLine(string $text)
	{
		$this->write($text . PHP_EOL);
	}

	public function writeError(string $text)
	{
		fwrite($this->error, $text);
	}

	public function writeErrorLine(string $text)
	{
		$this->writeError($text . PHP_EOL);
	}
}
