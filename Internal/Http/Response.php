<?php

namespace Internal\Http;

use Closure;
use Common\OutputBuffer;
use Exception;

class Response
{
	/**
	 * Internal use only
	 * 
	 * Use setStatus method to set and status method to get
	 */
	protected int $statusCode = 200;

	/**
	 * Internal use only
	 * 
	 * Use setHeader method to set and header method to get
	 */
	protected array $headers = ['Content-type' => 'application/json'];

	/**
	 * Internal use only
	 * 
	 * Use setCookie method to set and cookie method to get
	 */
	protected array $cookies = [];

	/**
	 * Internal use only
	 * 
	 * Use json method to set and body method to get
	 */
	protected array $body = [];

	/**
	 * Internal use only
	 * 
	 * Is end method already called
	 */
	protected bool $ended = false;

	/**
	 * Intenal use only
	 * 
	 * Is response for after middleware
	 * 
	 * Cannot end or send in after middleware
	 * 
	 * Because it will cause infinite loop
	 */
	protected bool $isAfterMiddleware = false;

	/**
	 * Intenal use only
	 * 
	 * After middleware closure
	 */
	protected Closure $afterMiddleware;

	function __construct(Closure $afterMiddleware)
	{
		$this->afterMiddleware = $afterMiddleware;
	}

	/**
	 * Set http status code
	 * 
	 * Cannot called if response already ended
	 */
	public function setStatus(int $statusCode): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		$this->statusCode = $statusCode;

		return $this;
	}

	/**
	 * Get http status code
	 */
	public function status(): int
	{
		return $this->statusCode;
	}

	/**
	 * Set header
	 * 
	 * Cannot called if response already ended
	 */
	public function setHeader(string $key, string $value): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		$this->headers[$key] = $value;

		return $this;
	}

	/**
	 * Get header
	 */
	public function header($key): string | null
	{
		if (in_array($key, $this->headers)) return $this->headers[$key];

		return null;
	}

	/**
	 * Set cookie
	 * 
	 * Cannot called if response already ended
	 */
	public function setCookie(
		string $name,
		string $value = "",
		int $expires_or_options = 0,
		string $path = "",
		string $domain = "",
		bool $secure = false,
		bool $httponly = false
	): self {
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		array_push($this->cookies, [
			'name' => $name,
			'value' => $value,
			'expires_or_options' => $expires_or_options,
			'path' => $path,
			'domain' => $domain,
			'secure' => $secure,
			'httponly' => $httponly,
		]);

		return $this;
	}
	/**
	 * Get cookie
	 */
	public function cookie(string $name): array | null
	{
		foreach ($this->cookies as $cookie) {
			if ($cookie['name'] === $name) return $cookie;
		}

		return null;
	}

	/**
	 * Set body
	 * 
	 * Cannot called if response already ended
	 */
	public function setBody(array $data): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		$this->body = $data;

		return $this;
	}

	/**
	 * Get body
	 */
	public function body(): array
	{
		return $this->body;
	}

	/**
	 * Flush status
	 * 
	 * Set status using http_response_code php method
	 * 
	 * Cannot called if response already ended
	 */
	public function flushStatus(): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		http_response_code($this->statusCode);

		return $this;
	}

	/**
	 * Flush headers
	 * 
	 * Set headers using header php method
	 * 
	 * Cannot called if response already ended
	 */
	public function flushHeaders(): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		foreach ($this->headers as $key => $value) {
			header("$key: $value");
		}

		return $this;
	}

	/**
	 * Flush cookies
	 * 
	 * Set headers using setcookie php method
	 * 
	 * Cannot called if response already ended
	 */
	public function flushCookies(): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		foreach ($this->cookies as $cookie) {
			setcookie(
				$cookie['name'],
				$cookie['value'],
				$cookie['expires_or_options'],
				$cookie['path'],
				$cookie['domain'],
				$cookie['secure'],
				$cookie['httponly'],
			);
		}

		return $this;
	}

	/**
	 * Flush body
	 * 
	 * Encode body to json and output
	 * 
	 * Cannot called if response already ended
	 */
	public function flushBody(): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		OutputBuffer::set(json_encode($this->body));

		return $this;
	}

	/**
	 * Call php flush
	 */
	public function flush()
	{
		flush();
	}

	/**
	 * Return true if response already ended otherwise false
	 */
	public function ended(): bool
	{
		return $this->ended;
	}

	/**
	 * End request
	 * 
	 * Cannot be called in after middleware
	 * 
	 * Cannot called if response already ended
	 */
	public function end(): void
	{
		if ($this->isAfterMiddleware) {
			throw new Exception('Cannot end in after middleware');

			return;
		}

		if ($this->ended) {
			throw new Exception('Request already ended');
		}

		$this->isAfterMiddleware = true;

		($this->afterMiddleware)($this);

		$this->isAfterMiddleware = false;

		$this->flushStatus();
		$this->flushHeaders();
		$this->flushCookies();

		$this->flush();

		$this->flushBody();

		$this->ended = true;
	}

	/**
	 * Set body, status code and end request
	 * 
	 * Cannot be called in after middleware
	 * 
	 * Cannot called if response already ended
	 */
	public function send(array $data, int $statusCode = 200)
	{
		if ($this->isAfterMiddleware) {
			throw new Exception('Cannot send in after middleware');

			return;
		}

		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		if ($statusCode) {
			$this->setStatus($statusCode);
		}

		$this->setBody($data);

		$this->end();
	}
}
