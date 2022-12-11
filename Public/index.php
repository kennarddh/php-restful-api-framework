<?php

declare(strict_types=1);

ini_set('display_errors', 1);

// Change this relative path to application and internal directory parent
define('PATH_FROM_INDEX_TO_APPLICATION', '..' . DIRECTORY_SEPARATOR);

include '../Internal/AutoLoader.php';

use Internal\Autoloader;
use Internal\Routes\Router;

AutoLoader::Register();

Router::Register();
