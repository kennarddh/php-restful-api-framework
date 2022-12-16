<?php

namespace Internal\Logger\Transports;

use Scripts\Console\ConsoleSingleton;

class ConsoleTransport extends BaseTransport
{
	public function log(string $level, string $message)
	{
		$date = date('m/d/Y h:i:s a', time());

		ConsoleSingleton::GetConsole()->writeLine("[$date] $level: $message");
	}
}
