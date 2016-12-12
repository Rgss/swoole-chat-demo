<?php
/**
 * DB 对外统一接口
 * 
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link	
 */

class Database {
	
	
	/**
	 * db 当前实例
	 * 
	 * @var Database
	 */
	protected static $instance;
	
	/**
	 * db 实例池
	 * 
	 * @var array
	 */
	protected static $instances = array();
	
	
	/**
	 * 实例 db 对象
	 * 
	 * @param array $config
	 */
	public static function getInstance($config = array()) {

		if (empty($config)) {
			$configs = Imp::app()->instance('config')->get('database');
			$config = $configs['default'];
		}
		
		// 当前db id
		$dbID = md5($config['host'] . $config['port'] . $config['dbname']);
		if (!isset(self::$instances[$dbID])) {
			
			// 加载数据库驱动类
			$class = ucfirst($config['type']);
			$driveFile = 'vendor/Imp/Database/' . $class . '/' . $class;
			Loader::load($driveFile);
						
			// 实例化数据库
			$class = strtolower($class) == 'mysqli' ? 'Mysql' : $class;
			self::$instances[$dbID] = new $class($config);
		}

		return self::$instance = self::$instances[$dbID];
	}
	
	
	
}