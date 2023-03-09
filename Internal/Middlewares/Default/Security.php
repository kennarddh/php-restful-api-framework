<?php

namespace Internal\Middlewares\Default;

use Internal\Middlewares\BaseMiddleware;
use Internal\Http\{Request, Response};

/**
 * Security middleware
 */
class Security extends BaseMiddleware
{
	public static function CORS(array $acceptOrigins, ?array $acceptMethods = [])
	{
		return function (Request $request, Response $response) use ($acceptOrigins, $acceptMethods) {
			if (empty($acceptMethods)) {
				$acceptMethods = [$request->method];
			}

			$response->setHeader('Access-Control-Allow-Origin', join(',', $acceptOrigins));
			$response->setHeader('Access-Control-Allow-Methods', join(',', $acceptMethods));

			if ($request->method === 'OPTIONS') {
				$response->setStatus(204);
				$response->end();
			}
		};
	}
}
