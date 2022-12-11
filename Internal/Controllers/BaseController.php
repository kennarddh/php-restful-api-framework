<?php

namespace Internal\Controllers;

use Internal\Http\Request;
use Internal\Http\Response;

/**
 * All controllers must to extend BaseController
 */
abstract class BaseController
{
	protected Request $request;
	protected Response $response;

	function __construct(Request $request, Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}
}
