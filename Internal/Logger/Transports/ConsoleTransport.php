<?php

namespace Internal\Logger\Transports;

class ConsoleTransport extends BaseTransport
{
	public function log(string $level, string $message)
	{
		$date = date('D M j G:i:s Y', time());

		if ($level === 'error')
			file_put_contents("php://stderr", "[$date] [$level]: $message" . PHP_EOL);
		else
			file_put_contents("php://stdout", "[$date] [$level]: $message" . PHP_EOL);
	}
}
