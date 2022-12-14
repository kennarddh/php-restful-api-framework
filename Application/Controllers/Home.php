<?php

namespace Application\Controllers;

use Exception;
use Internal\Controllers\BaseController;

final class Home extends BaseController
{
	public function index()
	{
		$this->response->send([
			"params" => $this->request->params,
		]);
	}

	public function create()
	{
		$this->response->send([
			'token' => $this->request->header('Authorization')
		], 201);
	}

	public function post()
	{
		$this->response->send([
			'body' => $this->request->body,
			'queryParameters' => $this->request->queryParameters,
			'tokenHeader' => $this->request->data['token'],
			'baseUrl' => $this->request->baseUrl,
			'ip' => $this->request->ip
		], 200);
	}

	public function all()
	{
		$this->response->send([
			'message' => 'all',
		], 200);
	}

	public function balance()
	{
		$this->response->send([
			'params' => $this->request->params,
		], 200);
	}

	public function allMethod()
	{
		$this->response->send([
			'message' => 'all method',
			'method' => $this->request->method
		]);
	}

	public function matchAll()
	{
		$this->response->send([
			'baseUrl' => $this->request->baseUrl,
			'method' => $this->request->method
		], 200);
	}

	public function error()
	{
		$this->response->send([
			'error' => 'Internal Server Error',
		], 500);
	}

	public function tryThrow()
	{
		$this->response->send([
			'error' => 'Error',
		], 200);

		throw new Exception('error');
	}

	public function file()
	{
		$filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Public'. DIRECTORY_SEPARATOR . 'DownloadImage.png';

		$this->response->sendFile('image.png', $filePath);
	}
}
