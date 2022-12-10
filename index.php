<?php

declare(strict_types=1);

ini_set('display_errors', 1);

include './Internal/AutoLoader.php';

use Internal\Autoloader;
use Internal\Routes\Router;

AutoLoader::Register();

Router::Register();
