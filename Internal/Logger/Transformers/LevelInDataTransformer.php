<?php

namespace Internal\Logger\Transformers;

class LevelInDataTransformer extends BaseTransformer
{
	public function transform(string $level, string $message, array $data, array $previous): array
	{
		return array_merge($previous, ['level' => $level]);
	}
}
