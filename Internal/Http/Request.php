<?php

namespace Internal\Http;

use Internal\Routes\Utils as RoutesUtils;

class Request
{
	/**
	 * Internal use only
	 * 
	 * Use header method to get header
	 */
	protected array $headers;

	/**
	 * Path parameters
	 */
	public object $params;

	/**
	 * Body
	 * 
	 * From raw json and form data
	 */
	public object $body;

	/**
	 * Query parameters
	 */
	public object $queryParameters;

	/**
	 * Is secure
	 * 
	 * Is secure become true if protocol is https otherwise it's false
	 */
	public bool $isSecure;

	/**
	 * Http method
	 */
	public string $method;

	/**
	 * Request url
	 */
	public string $baseUrl;

	/**
	 * Request ip
	 * 
	 * Maybe not correct if application behind reverse proxy
	 */
	public string $ip;

	/**
	 * Data between controller and middleware
	 */
	public array $data;

	function __construct()
	{
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
	 * Internal use only
	 * 
	 * Set path params
	 */
	public function setParams(object $params): self
	{
		$this->params = $params;

		return $this;
	}

	/**
	 * Get header by key
	 * 
	 * Case insensitive
	 * 
	 * - character replaced with _ character
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
