<?php

declare(strict_types=1);

ini_set('display_errors', 0);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Change this relative path to application and internal directory parent
define('PATH_FROM_INDEX_TO_APPLICATION', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

// Environment file `.env` directory
define('ENV_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

include __DIR__ . DIRECTORY_SEPARATOR . '../Common/AutoLoader.php';
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Common\Autoloader;
use Internal\Routes\Router;
use Application\Configuration\Logger\Configuration as LoggerConfiguration;
use Application\Configuration\Environment as LoggerEnvironment;
use Application\Configuration\Database\Configuration as DatabaseConfiguration;
use Internal\Configuration\Configuration;

// Register autoloader
AutoLoader::Register();

// Load configuration
LoggerEnvironment::Register();

ini_set('display_errors', LoggerEnvironment::$displayError === true ? 1 : 0);

LoggerConfiguration::Register();
DatabaseConfiguration::Register();
Configuration::LoadEnvFile();

// Register router
Router::Register();
