<?php
/**
 * Services Manager
 * 
 * @author zhang
 *
 */

class Service {
    
    
    /**
     * instance
     * 
     * @var Service
     */
    private static $instance = null;
    
    /**
     * fd
     * 
     * @var number
     */
    private $fd;
    
    /**
     * server
     * 
     * @var Web_Socket_Server
     */
    private $server;
    
    /**
     * services
     * 
     * @var array
     */
    private $services = [];
    
    
    /**
     * instance
     * 
     * @param Web_Socket_Server $server
     */
    public static function instance($server = null) {
        
        if (self::$instance !== null) {
            return self::$instance;
        }
                
        self::$instance = new Service();
        self::$instance->server = $server;
        
        return self::$instance;
    } 
    
    /**
     * get
     *  
     * @param string $name
     */
    public function get($name) {
        
        $name = ucfirst($name);
        if ($this->exists($name)) {
            $service = $this->services[$name];
        } else {
            $this->services[$name] = $service = new $name;
        }
        
        return $service;
    }
    
    /**
     * exists
     * 
     * @param string $name
     * @return boolean
     */
    public function exists($name) {
        $name = ucfirst($name);
        if (array_key_exists($name, $this->services)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * get server
     * 
     * @return Web_Socket_Server
     */
    public function getMainServer() {
        return $this->server;
    }
    
    /**
     * set server
     * 
     * @param Web_Socket_Server $server
     */
    public function setServer($server) {
        $this->server = $server;
    }
    

}