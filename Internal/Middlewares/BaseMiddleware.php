<?php

namespace Internal\Middlewares;

use Internal\Http\Request;
use Internal\Http\Response;

/**
 * All middlewares must to extend BaseMiddleware
 */
class BaseMiddleware
{
	protected Request $request;
	protected Response $response;

	function __construct(Request $request, Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}
}
