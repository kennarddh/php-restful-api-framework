<?php

namespace Internal\Http;

use Internal\Routes\Utils as RoutesUtils;

class Request
{
	protected array $headers;
	public object $params;
	public object $body;
	public object $queryParameters;
	public bool $isSecure;
	public string $method;
	public string $baseUrl;
	public string $ip;

	/**
	 * Data between controller and middleware
	 */
	public array $data;

	function __construct(object $params)
	{
		// Path params
		$this->params = $params;

		// Headers
		$this->headers = Utils::GetHeaders();

		// Body
		$this->body = Utils::GetBody();

		// Query parameters
		$this->queryParameters = Utils::GetQueryParameters();

		// Is secure
		$this->isSecure = Utils::IsSecure();

		// Method
		$this->method = $_SERVER['REQUEST_METHOD'];

		// Base url
		$this->baseUrl = RoutesUtils::GetUrl();

		// IP address
		$this->ip = $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Case insensitive
	 * 
	 * - replaced with _
	 */
	public function header(string $key): string | null
	{
		$computedKey = str_replace('-', '_', strtoupper($key));

		if (!isset($this->headers[$computedKey])) return null;

		$header = $this->headers[$computedKey];

		if ($header === '') return null;

		return $header;
	}
}
