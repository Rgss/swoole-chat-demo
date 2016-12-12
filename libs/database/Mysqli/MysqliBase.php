<?php
/**
 * Mysqli 基类
 *
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link	
 */

abstract class MysqliBase {

	
	/**
	 * config
	 * 
	 * @var array
	 */
    protected $_config;
    
    /**
     * database name
     * 
     * @var string
     */
    protected $_dbname;
    
    /**
     * table
     * 
     * @var string
     */
    protected $_table;
    
    /**
     * table 前缀
     * 
     * @var string
     */
    protected $_tablePrefix;
    
    /**
     * alias
     * 
     * @var string
     */
    protected $_alias;

    /**
     * debug
     * 
     * @var boolean
     */
    protected $_debug = false;
    
	/**
	 * debug info
	 * 
	 * @var boolean
	 */
    protected $_debugInfo;

    /**
     * 数据库连接池
     * 
     * @var array
     */
    protected $_links;
    
    /**
     * 当前连接
     * 
     * @var resource
     */
    protected $_link;
    
    /**
     * 当前查询句柄
     * 
     * @var resource
     */
    protected $_query;
    
	/**
	 * 当前sql
	 * 
	 * @var string
	 */
    protected $_sql;
    
	/**
	 * sql集合
	 * 
	 * @var array
	 */
    protected static $_sqls;
	
	/**
	 * 执行时间
	 * 
	 * @var number
	 */
	protected $_time;
	
	/**
	 * 查询的字段
	 * 
	 * @var string
	 */
	protected $_field = '*';
	
	/**
	 * 查询的字段
	 *
	 * @var string
	 */
	protected $_column = '*';
	
	/**
	 * where
	 * 
	 * @var data
	 */
	protected $_where;
	
	/**
	 * group
	 * 
	 * @var array
	 */
	protected $_group;
	
	/**
	 * having
	 * 
	 * @var array
	 */
	protected $_having;
	
	/**
	 * order
	 * 
	 * @var array
	 */
	protected $_order;
	
	/**
	 * limit
	 * 
	 * @var array
	 */
	protected $_limit;
	
	/**
	 * join
	 * 
	 * @var string
	 */
	protected $_join;
	
	/**
	 * sql builder
	 * 
	 * @var Builder
	 */
	protected $_builder;
	
	/**
	 * commit transaction
	 * 
	 * @var string
	 */
	protected $transactionCommit = true;
	
	/**
	 * result
	 * 
	 * @var mixed
	 */
	protected $resultSet = null;
	
	/**
	 * result type
	 * 
	 * @var string
	 */
	protected $resultType = 'object';

	/**
	 * 解析where条件，允许直接传入字符串格式和数据格式
	 * 
	 * @param mixed $data
	 * @return string
	 */
    public function _parseWhere($data) {
        if (is_array($data)) {
            return $this->_parseData($data, 'and');
        } else {
            return $data;
        }
    }
    
    /**
     * 解析要更新的字段
     * 
     * @param Array $data
     */
    public function _parseUpdate($data) {
        return $this->_parseData($data, ',');
    }

    /**
     * 解析要插入的字段
     * 
     * @param mixed $data
     * @return mixed
     */
    protected function _parseInsert($data) {
    	$gas = '';
        $result = array();
        $result['field'] = '';
        $result['value'] = '';
        foreach ($data as $key => $val) {
            $result['field'] .= $gas . $this->_parseField($key);
            $result['value'] .= $gas . "'" . $this->_parseValue($val) . "'";
            $gas = ',';
        }
        
        return $result;
    }


    /**
     * 解析数据
     * 
     * @param mixed $data
     * @param string $gas
     * @return string
     */
    protected function _parseData($data, $gas = ',') {
        $temp = '';
        foreach ($data as $k => $v) {
            if (is_numeric($v)) {
                $temp .= $this->_parseField($k) . ' = ' . $this->_parseValue($v) . ' ' . $gas . ' ';
            } else {
                $temp .= $this->_parseField($k) . " = '" . $this->_parseValue($v) . "' " . $gas . ' ';
            }
        }
        
        return trim($temp, $gas . ' ');
    }
    
    /**
     * 解析字段
     * 
     * @param unknown $field
     * @return string
     */
    protected function _parseField($field) {
		$field = trim($field);
		if (strpos($field, ' ') === false) {
			$field = '`' . $field . '`';
		}
		return $field;
	}
    
	/**
	 * 过滤值
	 * 
	 * @param string $value
	 * @return string
	 */
    protected function _parseValue($value) {
//     	if (!get_magic_quotes_gpc()) {
//     		return addslashes($value);
//     	}
//      return $value;

        return $this->_link->real_escape_string($value);
    }

    
}
