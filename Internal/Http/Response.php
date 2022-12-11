<?php

namespace Internal\Http;

use Closure;
use Exception;

class Response
{
	protected int $statusCode = 200;
	protected array $headers = ['Content-type' => 'application/json'];
	protected array $cookies = [];
	protected array $body = [];
	protected bool $ended = false;
	protected bool $isAfterMiddleware = false;
	protected Closure $afterMiddleware;

	function __construct(Closure $afterMiddleware)
	{
		$this->afterMiddleware = $afterMiddleware;
	}

	public function setStatus(int $statusCode): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		$this->statusCode = $statusCode;

		return $this;
	}

	public function status(): int
	{
		return $this->statusCode;
	}

	public function setHeader(string $key, string $value): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		$this->headers[$key] = $value;

		return $this;
	}

	public function header($key): string | null
	{
		if (in_array($key, $this->headers)) return $this->headers[$key];

		return null;
	}

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

	public function json(array $data): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		$this->body = $data;

		return $this;
	}

	public function body(): array
	{
		return $this->body;
	}

	public function flushStatus(): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		http_response_code($this->statusCode);

		return $this;
	}

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

	public function flushBody(): self
	{
		if ($this->ended) {
			throw new Exception('Request already ended');

			return $this;
		}

		echo json_encode($this->body);

		return $this;
	}

	public function ended(): bool
	{
		return $this->ended;
	}

	public function end(): void
	{
		if ($this->isAfterMiddleware) {
			throw new Exception('Cannot end in after middleware');

			return;
		}

		$this->isAfterMiddleware = true;

		($this->afterMiddleware)($this);

		$this->isAfterMiddleware = false;

		$this->flushStatus();
		$this->flushHeaders();
		$this->flushCookies();
		$this->flushBody();

		$this->ended = true;
	}

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

		$this->json($data);

		$this->end();
	}
}
