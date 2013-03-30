<?php

namespace TechDivision\ApplicationServer;

declare (ticks = 1);

error_reporting(~E_NOTICE);
set_time_limit (0);

// set the session timeout to unlimited
ini_set('session.gc_maxlifetime', 0);
ini_set('zend.enable_gc', 0);
ini_set('max_execution_time', 0);

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$server = new Server();
$server->start();