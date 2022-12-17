<?php

namespace Internal\Logger\Transformers;

class MessageInDataTransformer extends BaseTransformer
{
	public function transform(string $level, string $message, array $data, array $previous): array
	{
		return array_merge($previous, ['message' => $message]);
	}
}
