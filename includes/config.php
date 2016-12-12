<?php
/**
 * config
 * 
 * 
 */
 
$config['server'] = array(
    
    'main' => array(
        'host' => '0.0.0.0', 'port' => 1950
    ), 

    'login' => array(
        'host' => '0.0.0.0', 'port' => 1952
    ),
);
	

$config['database'] = array(
    
    'default' => array(
        'type' => 'mysqli',
        'host'=> '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'pconnect' => 0,
        'port' => 3306,
        'dbname' => 'test',
        'charset' => 'utf8',
        'tableprex' => '',
    ),
);

//return $config;