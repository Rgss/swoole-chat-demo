<?php
/**
 * DB 连接器
 * 
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link	
 */

require dirname(__FILE__) . '/ActiveRecord.php';

class Connection {
	
    
    /**
     * db map name
     * 
     * @var string
     */
    protected $dbname = 'default';
    
    /**
     * db configs
     * 
     * @var array
     */
    protected static $configs;
    
    /**
     * db config
     * 
     * @var array
     */
    protected $config;
    
    /**
     * activeRecord
     * 
     * @var ActiveRecord
     */
    protected $activeRecord = null;
	
	/**
	 * 当前连接
	 * 
	 * @var object
	 */
	protected static $connector;
	
	/**
	 * db连接池
	 * 
	 * @var array
	 */
	protected static $connectors = array();

	
	/**
	 * return a connection
	 * 
	 * @param array $config
	 */
	public static function getConnector($name = '') {
	    
	    $config = self::getConfig($name);
	    $dbID = md5($config['host'] . $config['port'] . $config['dbname']);
	    
		if (self::$connector === null || !empty($name)) {
		    
		    if (!isset(self::$connectors[$dbID])) {
		        self::$connector = new Connection();

		        // load database driver file
		        $class = ucfirst($config['type']);
		        $drive = 'vendor/Imp/Database/' . $class . '/' . $class;
		        $builder = 'vendor/Imp/Database/' . $class . '/Builder';
		        Loader::load($drive);
		        Loader::load($builder);

		        // create an instance
		        $class = strtolower($class) == 'mysqli' ? 'Mysql' : $class;
		        $db = new $class($config);

		        self::$connector->db = $db;
		        self::$connectors[$dbID] = self::$connector;
		    } else {
		        
		        self::$connector = self::$connectors[$dbID];
		    }
		}

		return self::$connector;
	}
	
	/**
	 * return database config
	 * 
	 * @param string $name
	 */
	public static function getConfig($name = null) {
	    if (self::$configs === null) {
	        self::$configs = Imp::app()->instance('config')->get('database');
	    }
	    
	    if ($name == null) {
	        $name = 'default';
	    }
	    
	    return isset(self::$configs[$name]) ? self::$configs[$name] : false;
	}

	/**
	 * ActiveRecord
	 * 
	 * @param Model $model
	 */
	public function getActiveRecord($model = null, $alias = null) {
	    if ($this->activeRecord === null || !empty($model)) {
	        $this->activeRecord = new ActiveRecord(self::$connector->db, $model, $alias);
	    }
	    return $this->activeRecord;
	}
	
	/**
	 * return current db link
	 * 
	 */
	public function db() {
	    return self::$connector->db;
	}
	
}