<?php

namespace Internal\Logger\Transports;

class ConsoleTransport extends BaseTransport
{
	public function log(string $level, string $message, array $data, string $formattedMessage): void
	{
		if ($level === 'error')
			file_put_contents("php://stderr", $formattedMessage . PHP_EOL);
		else
			file_put_contents("php://stdout", $formattedMessage . PHP_EOL);
	}
}
