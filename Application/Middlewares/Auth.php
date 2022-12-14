<?php

namespace Application\Middlewares;

use Internal\Middlewares\BaseMiddleware;

class Auth extends BaseMiddleware
{
	public function index()
	{
		if (empty($this->request->header('token'))) {
			return $this->response->send(['message' => 'Token required'], 401);
		}

		if ($this->request->header('token') !== 'token') {
			return $this->response->send(['message' => 'Invalid token'], 401);
		}

		$token = $this->request->header('token');

		$this->request->data['token'] = $token;
	}

	public function after()
	{
		$newBody = array_merge($this->response->body(), ['code' => $this->response->status()]);

		$this->response->setBody($newBody);
	}
}
