<?php

namespace Internal\Http;

use Closure;
use Common\OutputBuffer;
use Exception;
use Internal\Configuration\Configuration;

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
	protected array $headers = [];

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
	protected array $body;

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

	/**
	 * Internal use only
	 *
	 * File data
	 */
	protected array $fileData;

	/**
	 * Internal use only
	 *
	 * Set ended to false
	 */
	public function cancelEnd(): self
	{
		$this->ended = false;

		return $this;
	}

	/**
	 * Internal use only
	 *
	 * Set after middleware
	 */
	public function setAfterMiddleware(Closure $afterMiddleware): self
	{
		$this->afterMiddleware = $afterMiddleware;

		return $this;
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

		$this->setHeader('Content-type', 'application/json');

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
	 * Internal use only
	 *
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

		if (isset($this->body)) {
			// Send body
			$this->flushBody();
		} else if (isset($this->fileData)) {
			$this->sendFileChunk();
		}

		$this->ended = true;
	}

	/**
	 * Internal use only
	 *
	 * Send file chunk
	 */
	private function sendFileChunk()
	{
		// Flush header, status, and cookie before sending file chunk
		$this->flush();

		// Send file
		// Disable timeout
		set_time_limit(0);

		$ranges = $this->fileData['ranges'];

		$file = fopen($this->fileData['filePath'], "rb");

		// 1 chunk 8 kilobytes
		$oneChunkSize = 1024 * 8;

		if (count($ranges) === 0) {
			// No range or all range invalid

			while (!feof($file)) {
				$fileChunk = fread($file, $oneChunkSize);

				echo $fileChunk;

				$this->flush();
			}
		} else if (count($ranges) === 1) {
			// 1 range

			fseek($file, $ranges[0]['start']);

			$dataLength = $ranges[0]['end'] - $ranges[0]['start'] + 1;

			$sentBytes = 0;

			while ($sentBytes !== $dataLength) {
				$chunkSize = 0;

				if ($oneChunkSize > $dataLength - $sentBytes + 1) {
					$chunkSize = $dataLength - $sentBytes;
				} else {
					$chunkSize = $oneChunkSize;
				}

				$fileChunk = fread($file, $chunkSize);

				echo $fileChunk;

				$this->flush();

				$sentBytes += $chunkSize;
			}
		} else {
			// More than 1 range / multiple range

			$boundaryString = $this->fileData['boundaryString'];
			$index = -1;

			foreach ($ranges as $range) {
				$index += 1;

				// Add body header
				echo "--$boundaryString" . PHP_EOL;
				echo "Content-Type: " . $this->fileData['mime'] . PHP_EOL;
				echo "Content-Range: bytes " . $range['start'] . '-' . $range['end'] . '/' . $this->fileData['fileSize'] . PHP_EOL;
				echo PHP_EOL;

				// Move pointer to range start
				fseek($file, $range['start']);

				$dataLength = $range['end'] - $range['start'] + 1;

				$sentBytes = 0;

				while ($sentBytes !== $dataLength) {
					$chunkSize = 0;

					if ($oneChunkSize > $dataLength - $sentBytes + 1) {
						$chunkSize = $dataLength - $sentBytes;
					} else {
						$chunkSize = $oneChunkSize;
					}

					$fileChunk = fread($file, $chunkSize);

					echo $fileChunk;

					$this->flush();

					$sentBytes += $chunkSize;
				}

				if (count($ranges) !== $index + 1) {
					echo PHP_EOL;
				}
			}

			echo PHP_EOL;
			echo "--$boundaryString";
		}

		fclose($file);
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

	/**
	 * Send file
	 *
	 * @param bool $strict Default false. If strict set to true will response with 416 if range invalid, otherwise will skip invalid range
	 */
	public function sendFile(string $fileName, string $filePath, bool $strict = false)
	{
		if ($this->isAfterMiddleware) {
			throw new Exception('Cannot send file in after middleware');

			return;
		}

		if ($this->ended) {
			throw new Exception('Request already ended');
		}

		if (!file_exists($filePath)) {
			throw new Exception('File doesn\'t exist');

			return;
		}

		$request = Singleton::GetRequest();

		$fileSize = filesize($filePath);

		$ranges = [];
		$boundaryString = Configuration::$HTTPResponseRangeBoundaryString;

		$ranges = Utils::ParseRangeHeader($request->header('Range'), $fileSize, ['strict' => $strict], function () {
			$this->setStatus(416);

			$this->end();
		});

		if ($this->ended()) return;

		// Get file content type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $filePath);
		finfo_close($finfo);

		if (count($ranges) === 0) {
			// No range or all range invalid

			// Set status code
			$this->setStatus(200);

			$this->setHeader('Content-Type', $mime);

			// Define file size
			$this->setHeader('Content-Length', $fileSize);
		} else if (count($ranges) === 1) {
			// 1 range

			$this->setHeader('Content-Type', $mime);

			$rangeStart = $ranges[0]['start'];
			$rangeEnd = $ranges[0]['end'];

			// Define file size
			$this->setHeader('Content-Length', $rangeEnd - $rangeStart + 1);
			$this->setHeader('Content-Range', "bytes $rangeStart-$rangeEnd/$fileSize");

			// Set status code
			$this->setStatus(206);
		} else {
			// More than 1 range / multiple range

			// Set status code
			$this->setStatus(206);

			$this->setHeader('Content-Type', $mime);
		}

		$this->setHeader('Content-Disposition', "attachment; filename=$fileName");

		// No cache
		$this->setHeader('Expires', '0');
		$this->setHeader('Cache-Control', 'must-revalidate');
		$this->setHeader('Pragma', 'public');

		// Accept range in bytes
		$this->setHeader('Accept-Ranges', 'bytes');

		$this->fileData = [
			"fileName" => $fileName,
			"filePath" => $filePath,
			"fileSize" => $fileSize,
			"ranges" => $ranges,
			"boundaryString" => $boundaryString,
			"mime" => $mime,
		];

		$this->end();
	}
}
