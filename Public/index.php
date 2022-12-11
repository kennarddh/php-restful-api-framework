<?php

declare(strict_types=1);

ini_set('display_errors', 1);

// Change this relative path to application and internal directory parent
define('PATH_FROM_INDEX_TO_APPLICATION', '..' . DIRECTORY_SEPARATOR);

include '../Common/AutoLoader.php';

use Common\Autoloader;
use Internal\Routes\Router;

AutoLoader::Register();

Router::Register();
