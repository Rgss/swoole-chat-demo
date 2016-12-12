<?php
/**
 * server
 * 
 * 
 */

// load file
require './includes/bootstrap.php';

$client = new swoole_client(SWOOLE_SOCK_TCP , SWOOLE_SOCK_ASYNC); // 异步非阻塞 SWOOLE_SOCK_ASYNC

$client->on('connect', function($cli) {
    $content = array(
        'username' => 'air',
        'password' => '123456',
        'msg' => !empty($data) ? $data : '',
        'time' => time(),
    );
    
    $data = array();
    $data['cmd'] = CMD::CMD_TALK_ALL;
    $data['content'] = $content;
    $buffer = Packet::encode($data);
    
    $cli->send($buffer);
    error_log('[client] connect.');
});

$client->on('receive', function($cli, $data) {
    echo "receive: {$data}\n";
    error_log('[client] receive.');
    error_log("[client] data: #" . print_r($data, 1));
});

$client->on('error', function($cli) {
    error_log('[client] error.');
});

$client->on('close', function($cli) {
    error_log('[client] close.');
});


// connect
$client->connect($config['server']['main']['host'], $config['server']['main']['port']);


function onConnect($client) {
    echo "[client] conncet: \n";
}

function onReceive($client, $data) {
    echo "[client] receive: \n";
    $data = Format::parseServer($data);
    print_r($data);
    echo "\n";
}

function onError($client) {
    echo "[client] error.\n";
}

function onClose() {
    echo "[client] close.\n";
}