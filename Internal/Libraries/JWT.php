<?php

namespace Internal\Libraries;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
	/**
	 * Encode JWT
	 */
	public static function Encode(array $payload, string $key, string $algo): string
	{
		return FirebaseJWT::encode($payload, $key, $algo);
	}

	/**
	 * Validate JWT
	 */
	public static function Decode(string $jwt, string $key, string $algo): array
	{
		return (array)FirebaseJWT::decode($jwt, new Key($key, $algo));
	}
}
