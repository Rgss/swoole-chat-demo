<?php
/**
 * Server
 * 
 * @author zhang
 *
 */

class Web_Socket_Server {

    /**
     * server
     * 
     * @var swoole_server
     */
    protected $serv = null;
    
    /**
     * current fd
     * 
     * @var resource
     */
    protected $fd = null;
    
    /**
     * services list
     * 
     * @var array
     */
    protected $services = array();
    
    /**
     * construct
     * 
     * @param string $host
     * @param number $port
     * @param array $config
     */
    public function __construct($host, $port, $config = array()) {
        
        $this->serv = new swoole_websocket_server($host, $port, SWOOLE_BASE);  // SWOOLE_PROCESS, SWOOLE_SOCK_TCP
        
        $this->serv->set(array(
            
            'reactor_num' => 2, //reactor thread num
            
            'worker_num' => 4,    //worker process num
            
            'backlog' => 128,   //listen backlog
            
            'max_request' => 10000,
            
            'daemonize' => 0,
            
            'heartbeat_check_interval' => 600,
            
            'heartbeat_idle_time' => 84600,
            
            'task_worker_num' => 2
        ));
        
        $this->serv->on('open', array($this, 'onOpen'));
        
        $this->serv->on('handshake', array($this, 'onHandshake'));
        
        $this->serv->on('message', array($this, 'onMessage'));
        
        $this->serv->on('close', array($this, 'onClose'));
        
        $this->serv->on('task', array($this, 'onTask'));
        
        $this->serv->on('finish', array($this, 'onFinish'));
        
        $this->serv->on('packet', array($this, 'onPacket'));
        
        $this->serv->on('request', array($this, 'onRequest'));
        
        $this->serv->on('request', array($this, 'onRequest'));
    }
    
    public function start() {
        echo "[server] start.\n";
        
        $this->serv->start();
    }
    
    public function onOpen(swoole_websocket_server $serv, swoole_http_request $request) {
        
        echo "[server] onOpen #{$serv->worker_pid}: handshake success with fd#{$request->fd}\n";
        // var_dump($serv->exist($request->fd), $serv->getClientInfo($request->fd));
    }
    
    public function onHandshake(swoole_http_request $request, swoole_http_response $response) {
        // error_log("[server] onHandshake: ");
        
        // 自定定握手规则，没有设置则用系统内置的（只支持version:13的）
        if (! isset($request->header['sec-websocket-key'])) {
            //'Bad protocol implementation: it is not RFC6455.'
            $response->end();
            return false;
        }
        
        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key']) || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))) {
            //Header Sec-WebSocket-Key is illegal;
            $response->end();
            return false;
        }
        
        $key = base64_encode(sha1($request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $headers = array(
            'Upgrade' => 'websocket', 'Connection' => 'Upgrade', 'Sec-WebSocket-Accept' => $key, 'Sec-WebSocket-Version' => '13', 'KeepAlive' => 'off'
        );
        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }
        
        $response->status(101);
        $response->end();
        
        $fd = $request->fd;
        $server = $this->serv;
        $this->serv->defer(function () use($fd, $server) {
            $content = array();
            $content['sid'] = 'token_'. mt_rand(10000, 99999);
            $content['status'] = 1;
            $content['name'] = '系统消息';
            $content['content'] = '连接成功';
            $buffer = Packet::encode($content);
            
            $server->push($fd, $buffer);
        });
        
        return true;
    }
    
    public function onMessage(swoole_server $serv, $frame) {
        
        $fd     = $frame->fd;
        $buffer = $frame->data;
        $data   = Packet::decode($buffer);
        
        // 连接信息
//         $connection = $serv->connection_info($fd);
//         error_log("frame: " . print_r($frame, 1));
//         error_log("connection: " . print_r($connection, 1));
//         error_log('cons: ' . print_r($this->connections($serv), 1));
        
        if (empty($data['cmd'])) {
            $serv->send($fd, 'receive error data');
            return;
        }
        
        $cmd = $data['cmd'];    // 指令
        if (!isset(CMD::$config[$cmd])) {
            return;
        }
        
        $this->fd = $fd;
        
        $service_info = CMD::$config[$cmd];
        Loader::service($service_info['service']);
        $name    = ucfirst($service_info['service']);
        $serviceManager = Service::instance($this);
        $service = $serviceManager->get($name);
        
        // set token
        $token = isset($data['token']) ? $data['token'] : null;
        Loader::service('token');
        $serviceManager->get('token')->setToken($token);
        
        $action  = $service_info['action'];
        $content = isset($data['data']) ? $data['data'] : array();
        Param::setContent($content);
        
        $content = call_user_func_array(array($service, $action), array());
        if (!isset($content['status'])) {
            $content['status'] = 0;
            $content['content'] = '系统错误';
        }
		$content['time'] = time();
		$content['date'] = date('Y-m-d H:i:s');
        
        $buffer = Packet::encode($content);
        
        $serv->push($fd, $buffer);
        
        /*
        $serv->tick(2000, function ($id) use($fd, $serv) {
            
            $tick_data = array(
                
            );
            
            $buffer = Packet::encode($tick_data);
            
            $_send = "server tick message";
            $ret = $serv->push($fd, $buffer);
            if (! $ret) {
                var_dump($id);
                var_dump($serv->clearTimer($id));
            }
        });
        */
    }
    
    public function onClose($serv, $fd, $from_id) {
        echo "[server] close.\n";
    }
    
    public function onTask($serv, $fd, $task_id, $data) {
        echo "[server] task.\n";
    }
    
    public function onFinish($serv, $task_id, $data) {
        echo "[server] finish.\n";
    }
    
    public function onPacket($serv, $data, $client) {
        echo "[server] onPacket.\n";
        
        echo "#".posix_getpid()."\tPacket {$data}\n";
        var_dump($client);
    }
    
    public function onRequest(swoole_http_request $request, swoole_http_response $response) {
        echo "[server] onRequest.\n";
        
        $response->end(<<<HTML
                <h1>Swoole WebSocket Server</h1>
                <script>
            var wsServer = 'ws://120.25.210.3:1950';
            var websocket = new WebSocket(wsServer);
            websocket.onopen = function (evt) {
            	console.log("Connected to WebSocket server.");
            };
            websocket.onclose = function (evt) {
            	console.log("Disconnected");
            };
            websocket.onmessage = function (evt) {
            	console.log('Retrieved data from server: ' + evt.data);
            };
            websocket.onerror = function (evt, e) {
            	console.log('Error occured: ' + evt.data);
            };
            </script>
HTML
            );
    }
    
    /**
     * onWorkerStart
     * 
     * @param unknown $serv
     * @param unknown $worker_id
     */
    public function onWorkerStart($serv, $worker_id) {
        // error_log("[server] onWorkerStart: ");
        // 在Worker进程开启时绑定定时器
        echo "[server] onWorkerStart\n";
        // 只有当worker_id为0时才添加定时器,避免重复添加
        if( $worker_id == 0 ) {
            $serv->addtimer(100);
            $serv->addtimer(500);
            $serv->addtimer(1000);
        }
    }
    
    /**
     * timer
     * 
     * @param unknown $serv
     * @param unknown $interval
     */
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
     * connections list
     * 
     * @param Web_Socket_Server $server
     * @param boolean $self_flag
     */
    public function connections($server = null, $self_flag = true) {
        if (1 || empty($server)) {
            $server = $this->serv;
        }
        
        $start_fd = 0;
        $conn_list = array();
        while (true) {
            $temp = $server->connection_list($start_fd, 100);
            
            if (!$self_flag && !empty($temp) && in_array($this->fd, $temp)) {
                foreach ($temp as $k => $v) {
                    if ($v == $this->fd) {
                        unset($temp[$k]);
                    }
                }
            }
            
            if ($temp === false or count ($temp) === 0) {
                echo "finish\n";
                break;
            }
            $start_fd = end($conn_list);
            
            $conn_list = array_merge($conn_list, $temp);
        }
        
        return array_unique($conn_list);
    }

    /**
     * broadcast
     * 
     * @param object $server
     * @param array $conn_list
     * @param array $data
     * @param boolean $self_flag
     */
    public function broadcast($server, $conn_list, $data, $self_flag = null) {
        if (empty($server)) {
            $server = $this->server;
        }
        
        if (!isset($data['status'])) {
            $data['status'] = 0;
            $data['content'] = '系统错误';
        }
		
		$data['time'] = time();
		$data['date'] = date('Y-m-d H:i:s');
        
        $buffer = Packet::encode($data);
        
        // echo "[server] broadcast data: {$buffer} ";
        foreach ($conn_list as $fd) {
            if (!$self_flag || $fd == $this->fd) {
                continue;
            }
            
            // echo "[server] broadcast #fd-{$fd}.\n";
            $res = $server->push($fd, $buffer);
            if (!$res) {
                echo "[server] receive send fail.\n";
            }
        }
    }
    
    /**
     * check service
     * 
     * @param string $name
     * @return boolean
     */
    public function serviceExists($name) {
        if (array_key_exists($name, $this->services)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * return swoole server
     * 
     * @return swoole_server
     */
    public function getServer() {
        return $this->serv;
    }
    
    /**
     * return current fd
     * 
     * @return resource
     */
    public function getCurrentFD() {
        return $this->fd;
    }
    
}
