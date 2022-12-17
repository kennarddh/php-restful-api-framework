<?php

namespace Internal\Logger\Transports;

class ConsoleTransport extends BaseTransport
{
	public function log(string $level, string $message, string $formatted)
	{
		if ($level === 'error')
			file_put_contents("php://stderr", $formatted . PHP_EOL);
		else
			file_put_contents("php://stdout", $formatted . PHP_EOL);
	}
}
