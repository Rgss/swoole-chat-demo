<?php
/**
 * Server
 * 
 * @author zhang
 *
 */

class Server {

    /**
     * server
     * @var swoole_server
     */
    protected $serv = null;
    
    /**
     * construct
     * 
     * @param string $host
     * @param number $port
     * @param array $config
     */
    public function __construct($host, $port, $config = array()) {
        
        $this->serv = new swoole_server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        
        $this->serv->set(array(
            
            'reactor_num' => 2, //reactor thread num
            
            'worker_num' => 4,    //worker process num
            
            'backlog' => 128,   //listen backlog
            
            'max_request' => 10000,
            
            'daemonize' => 0,
            
            'heartbeat_check_interval' => 20,
            
            'heartbeat_idle_time' => 100,
            
            'task_worker_num' => 2
        ));
        
        $this->serv->on('connect', array($this, 'onConnect'));
        
        $this->serv->on('receive', array($this, 'onReceive'));
        
        $this->serv->on('close', array($this, 'onClose'));
        
        $this->serv->on('task', array($this, 'onTask'));
        
        $this->serv->on('finish', array($this, 'onFinish'));
    }
    
    public function start() {
        echo "[server] start.\n";
        
        $this->serv->start();
    }
    
    public function onConnect($serv, $fd, $from_id) {
        echo "[server] connect.\n";
    }
    
    public function onReceive(swoole_server $serv, $fd, $from_id, $buffer) {
        echo "[server] receive {$buffer}\n";
        
        $data = Packet::decode($buffer);
        if (empty($data['cmd'])) {
            echo "[server] receive error data.\n";
            $serv->send($fd, 'receive error data');
            return;
        }
        
        $cmd = $data['cmd'];    // 指令
        if (!isset(CMD::$config[$cmd])) {
            return;
        }
        
        $service_info = CMD::$config[$cmd];
        Loader::service($service_info['service']);
        $name = ucfirst($service_info['service']);
        $service = new $name;
        $action = $service_info['action'];
        
        
        $content = isset($data['content']) ? $data['content'] : array();
        Param::setContent($content);
        Param::set('server', $serv);
        
        $content = call_user_func_array(array($service, $action), array());
        $buffer = Packet::encode(array('content' => $content));
        
        error_log("server return data: " . print_r($content, 1));
        
        $serv->send($fd, $buffer);
    }
    
    public function onClose($serv, $fd, $from_id) {
        echo "[server] close.\n";
    }
    
    public function onTask($serv, $task_id, $fd, $data) {
        echo "[server] task.\n";
    }
    
    public function onFinish($serv,$task_id, $data) {
        echo "[server] finish.\n";
    }
    
    public function onWorkerStart($serv, $worker_id) {
        // 在Worker进程开启时绑定定时器
        echo "onWorkerStart\n";
        // 只有当worker_id为0时才添加定时器,避免重复添加
        if( $worker_id == 0 ) {
            $serv->addtimer(100);
            $serv->addtimer(500);
            $serv->addtimer(1000);
        }
    }
    
    public function onTimer($serv, $interval) {
        switch( $interval ) {
            case 500: {	//
                echo "Do Thing A at interval 500\n";
                break;
            }
            case 1000:{
                echo "Do Thing B at interval 1000\n";
                break;
            }
            case 100:{
                echo "Do Thing C at interval 100\n";
                break;
            }
        }
    }
    
    /**
     * 获取连接列表
     *
     */
    public function connections($server = null) {
        if (empty($server)) {
            $server = $this->swoole_server;
        }
        $start_fd = 0;
        $conn_list = array();
        while (true) {
            $temp = $server->connection_list($start_fd, 100);
            if ($temp === false or count ($temp) === 0) {
                echo "finish\n";
                break;
            }
            $start_fd = end($conn_list);
            //var_dump($conn_list);
            $conn_list = array_merge($conn_list, $temp);
        }
        echo "[server] connections " . print_r($conn_list, 1);
        return array_unique($conn_list);
    }
    
    /**
     * 广播
     * @param unknown $fds
     * @param unknown $data
     */
    public function broadcast($server, $conn_list, $data) {
        if (empty($server)) {
            $server = $this->swoole_server;
        }
        echo "[server] broadcast data: {$data} " . print_r($server, 1);
        foreach ($conn_list as $fd) {
            echo "[server] broadcast #fd{$fd}.\n";
            $res = $server->send($fd, $data);
            if (!$res) {
                echo "[server] receive send fail.\n";
            }
        }
    }
    
}