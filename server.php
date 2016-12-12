<?php
/**
 * server
 * 
 * 
 */

require './includes/bootstrap.php';
require './libs/server.php';

$server = new Server($config['server']['main']['host'], $config['server']['main']['port']);

$server->start();