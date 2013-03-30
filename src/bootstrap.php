<?php

namespace TechDivision;

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

$paths[] = __DIR__ . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = __DIR__ . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = __DIR__ . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = __DIR__ . DS . 'app' . DS . 'code' . DS . 'lib';

// set the new include path
set_include_path(implode(PS, $paths) . PS . get_include_path());

require 'TechDivision/SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();