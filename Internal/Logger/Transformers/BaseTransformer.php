<?php

namespace Internal\Logger\Transformers;

/**
 * All logger transformer must extend BaseTransformer
 */
abstract class BaseTransformer
{
	/**
	 * This method is called when a new log logged
	 * 
	 * Return value from previous transformer is passed to current transformer
	 * 
	 * Transform data
	 */
	abstract public function transform(string $level, string $message, array $data, array $previous): array;
}
