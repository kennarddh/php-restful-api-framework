<?php

namespace Internal\Logger\Formatters;

class StringFormatter extends BaseFormatter
{
	public function format(string $level, string $message, array $data, string $previous): string
	{
		$date = date('D M j G:i:s Y', time());

		return "[$date] [$level]: $message, " . json_encode($data);
	}
}
