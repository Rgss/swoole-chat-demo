<?php
/**
 * server
 * 
 * @author zhang
 * 
 */

require './includes/bootstrap.php';
require './libs/web_socket_server.php';

$server = new Web_Socket_Server($config['server']['main']['host'], $config['server']['main']['port']);

$server->start();