<?php

$_SERVER['SCRIPT_NAME'] = '/index.php';

$publicFolder = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$path = $publicFolder . ltrim($uri, '/');

if ($uri !== '/' && (is_file($path) || is_dir($path))) {
	return false;
}

include $publicFolder . 'index.php';