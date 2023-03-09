<?php

namespace Application\Middlewares;

use Internal\Middlewares\BaseMiddleware;

class Auth extends BaseMiddleware
{
	public static function index()
	{
		return function ($request, $response) {
			if (empty($request->header('token'))) {
				return $response->send(['message' => 'Token required'], 401);
			}

			if ($request->header('token') !== 'token') {
				return $response->send(['message' => 'Invalid token'], 401);
			}

			$token = $request->header('token');

			$request->data['token'] = $token;
		};
	}

	public static function after()
	{
		return function ($request, $response) {
			$newBody = array_merge($response->body(), ['code' => $response->status()]);

			$response->setBody($newBody);
		};
	}
}
