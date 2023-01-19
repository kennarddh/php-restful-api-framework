<?php

declare(strict_types=1);

ini_set('display_errors', 1);

// Change this relative path to application and internal directory parent
define('PATH_FROM_INDEX_TO_APPLICATION', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

// Environment file `.env` directory
define('ENV_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

include __DIR__ . DIRECTORY_SEPARATOR . '../Common/AutoLoader.php';
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Common\Autoloader;
use Internal\Routes\Router;
use Application\Configuration\Logger\Configuration as LoggerConfiguration;
use Internal\Configuration\Configuration;

// Register autoloader
AutoLoader::Register();

// Load configuration
LoggerConfiguration::Register();
Configuration::LoadEnvFile();

// Register router
Router::Register();
