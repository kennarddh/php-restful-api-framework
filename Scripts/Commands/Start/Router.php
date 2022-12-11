<?php

// Router to mimic Apache's mod_rewrite functionality

// Set script name to index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Public folder where index.php located
$publicFolder = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;

// Get current uri
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Path to file
$path = $publicFolder . ltrim($uri, '/');

// If file with path exist
// If path exist return false to run default php routing
if ($uri !== '/' && (is_file($path) || is_dir($path))) {
	return false;
}

// If path doesn't exist include index.php
include $publicFolder . 'index.php';