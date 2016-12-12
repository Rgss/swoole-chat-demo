<?php
/**
 * DB operate
 *
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link
 */

class DB {
	
	
	/**
	 * db instance
	 * 
	 * @var object
	 */
	protected static $db = null;
	
	
	/**
	 * instance
	 * 
	 * @return string
	 */
	public static function instance($dbName = null) {
		return Connection::getConnector($dbName);
	} 
	
	/**
	 * select table
	 * 
	 * @param string $table
	 */
	public static function table($table, $alias = null) {
	    return Connection::getConnector()->getActiveRecord($table, $alias);
	}
	
	/**
	 * database name
	 * 
	 * @param string $name
	 */
	public static function connection($name = null) {
	    return Connection::getConnector($name);
	}
	
	/**
	 * query sql
	 * 
	 * @param string $sql
	 */
	public static function query($sql) {
		return self::instance()->query($sql);	
	}
	
	/**
	 * fetch one record
	 * 
	 * @param string $sql
	 */
	public static function fetch($sql = null) {
		// $sql = !empty($sql) ? $sql : self::getSql();
		return self::instance()->fetch($sql);
	}
		
	/**
	 * transaction
	 * 
	 * @param function $callback
	 */
	public static function transaction($callback) {
	    Connection::getConnector()->db()->beginTransaction();
	    
	    call_user_func_array($callback, array());
	    
	    Connection::getConnector()->db()->commit();
	    
	    Connection::getConnector()->db()->rollback();
	}
	
	/**
	 * start transaction
	 * 
	 */
	public static function begin() {
	    Connection::getConnector()->db()->beginTransaction();
	}
	
	/**
	 * commit transaction
	 * 
	 */
	public static function commit() {
	    Connection::getConnector()->db()->commit();
	}
	
	/**
	 * rollback
	 * 
	 */
	public function rollback() {
	    Connection::getConnector()->db()->rollback();
	}
	
}

