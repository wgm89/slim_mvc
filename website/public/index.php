<?php
/**
 * start
 * author : saeed
 * date   : 2013-7-9
 */
//start
require '../path.php';
require (SYS.'/Slim.php');

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

// load route file
load_route_from_cfg($app);

// excute
$app->run();
