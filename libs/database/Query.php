<?php
/**
 * DB query
 *
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2016 Imp All rights reserved.
 * @version 	1.0
 * @link
 */

class Query {

    
    /**
     * db 实例
     *
     * @var object
     */
    protected $db;
    
    /**
     * 数据表
     *
     * @var string
     */
    protected $table;
    
    /**
     * 数据库节点
     *
     * @var array
     */
    protected $dbname;
    
    /**
     * db 配置
     *
     * @var array
     */
    protected $config;
    
    /**
     * sql
     *
     * @var string
     */
    protected $sql;
      
    /**
     * 字段
     *
     * @var string
     */
    protected $column = '*';

    
    /**
     * 初始化
     * 
     * @param object $db
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * 设置表名
     *
     * @param string $name
     */
    public function table($name) {
        return $this->db()->table($name);
    }
    
    /**
     * 切换数据库
     *
     * @param string $db_name
     */
    public function selectDB($db_name) {
        $config = isset($this->config[$db_name]) ? $this->config[$db_name] : $this->config['default'];
        $this->db($db_name . '.' . $this->table)->selectDB($config['dbname']);
    }
    
    /**
     * 切换数据表
     *
     * @param string $name
     */
    public function selectTable($name) {
        $this->db()->selectTable($name);
        $this->table = $name;
        return $this;
    }
    
    /**
     * 兼容
     *
     * @see selectTable
     * @param string $name
     */
    public function setTable($name) {
        $this->selectTable($name);
    }
    
    /**
     * 获取一条记录
     *
     * @param string $sql
     */
    public function query($sql='') {
        if (!empty($sql)) {
            return $this->db()->query($sql);
        }
        return false;
    }
    
    /**
     * 获取一条记录
     *
     * @param string $sql
     */
    public function fetch($sql = null) {
        $sql = empty($sql) ? $this->getSql() : $sql;
        return $this->db()->fetch($sql);
    }
    
    /**
     * 获取多条记录
     *
     * @param string $sql
     * @return mixed >
     */
    public function fetchArray($sql = null, $index = false) {
        $sql = empty($sql) ? $this->getSql() : $sql;
        return $this->db()->fetchArray($sql, $index);
    }
    
    /**
     * 自增操作
     *
     * @param array $field
     * @param array $where
     * @return boolean
     */
    public function increment($field) {
        return $this->db()->increment($this->table, $field);
    }
    
    /**
     * 获取sql语句
     *
     * @return mixed
     */
    public function getSql() {
        $this->sql = 'SELECT ' . $this->getColumnField() . ' FROM ' 
            . $this->db()->table($this->table) 
            . $this->db()->getWhere() . ' ' 
            . $this->db()->getGroup() . ' ' 
            . $this->db()->getHaving() . ' ' 
            . $this->db()->getOrder() . ' ' 
            . $this->db()->getLimit();
        
        return $this->sql;
    }
    
    /**
     * 设置sql语句
     *
     * @param string $sql
     */
    public function setSql($sql='') {
        $this->sql = $sql;
    }
    
    /**
     * 设置选择的字段
     *
     * @param string $data
     * @return Model
     */
    public function setColumn($data = '*') {
        $column_str = '';
        if (is_array($data)) {
            foreach($data as $k => $v) {
                $column_str .= '`' . $v . '`,';
            }
            $this->column = trim($column_str , ',');
        } else {
            $this->column = $data;
        }
    
        return $this;
    }
    
    /**
     * 获取字段
     *
     */
    public function getColumn() {
        return $this->column;
    }
    
    /**
     * 分页
     *
     * @param number $page
     * @param number $pageSize
     * @param number $total
     * @param string $sql
     */
    public function page($page = 1, $pageSize = 10, $total = 0, $sql = null) {
        $sql = empty($sql) ? $this->getSql() : $sql;
        return $this->db()->page($sql, $page, $pageSize, $total);
    }
    
    /**
     * 添加记录
     *
     * @param array $data
     * @param string $lastInsert
     * @return mixed
     */
    public function insert($data, $lastInsert = false) {
        if (empty($data)) { return false;}
        return $this->db()->insert($this->table, $data, $lastInsert);
    }
    
    /**
     * 添加多条记录
     *
     * @param array $data
     * @return boolean
     */
    public function multiInsert($data) {
        if (empty($data)) { return false;}
        return $this->db()->multiInsert($this->table, $data);
    }
    
    /**
     * 获取最后insert id
     *
     * @return mixed
     */
    public function lastInsertId() {
        return $this->db()->lastInsertId();
    }
    
    /**
     * 更新数据
     *
     * @param array $data
     */
    public function update($data = array(), $limit = 1) {
        $where = $this->db()->getWhere(false);
    
    
        if (empty($where)) { return false;}
        $data = !empty($data) ? $data : $this->property;
        return $this->db()->update($this->table, $data, $where, $limit);
    }
    
    /**
     * 统计数量
     *
     * @param string $countStr
     * @return mixed
     */
    public function count($countStr = 'count(1) as `count`') {
        $sql = "SELECT {$countStr} FROM " . $this->db()->table($this->table) . $this->db()->getWhere() . ' ' .
            $this->db()->getGroup() . ' ' . $this->db()->getHaving() . ' ' . $this->db()->getOrder();
            return $this->db()->query($sql);
    }
    
    /**
     * 删除记录
     *
     * @return mixed
     */
    public function delete($limit = 1) {
        return $this->db()->delete($this->table, $this->db()->getWhere(false), $limit);
    }
    
    /**
     * 设置limit
     *
     * @param string $str
     * @return Model
     */
    public function limit($str) {
        $this->db()->setLimit($str);
        return $this;
    }
    
    /**
     * 设置条件
     *
     * @param array $data
     * @return Model
     */
    public function where($data) {
        $this->db->setWhere($data);
        return $this;
    }
    
    /**
     * 设置group
     *
     * @param string $str
     * @return Model
     */
    public function group($str) {
        $this->db()->setGroup($str);
        return $this;
    }
    
    /**
     * 设置having
     *
     * @param string $str
     * @return Model
     */
    public function having($str) {
        $this->db()->setHaving($str);
        return $this;
    }
    
    /**
     * 设置order
     *
     * @param string $str
     * @return Model
     */
    public function order($str) {
        $this->db()->setOrder($str);
        return $this;
    }
    
    /**
     * 启动事务
     *
     */
    public function beginTransaction() {
        $this->db()->beginTransaction();
    }
    
    /**
     * 提交事务
     *
     */
    public function commit() {
        $this->db()->commit();
    }
    
    /**
     * 回滚
     *
     */
    public function rollback() {
        $this->db()->rollBack();
    }
        
    /**
     * get
     *
     * @return mixed
     */
    public function get() {
        $instance = new static;
        return $instance->fetchArray();
    }
    
    /**
     * find one record
     *
     * @param nubmer $id
     */
    public function find($id) {
        return $this->db->where(array($this->primaryKey => $id))->fetch();
    }
    
    /**
     * 保存数据
     *
     * @param string $lastInsert
     * @return mixed
     */
    public function save($lastInsert = false) {
        if (empty($this->property)) {
            return false;
        }
    
        return $this->insert($this->property, $lastInsert);
    }
    
    /**
     * get
     *
     * @param string $name
     */
    public function __get($name) {
        return isset($this->property[$name]) ? $this->property[$name] : null;
    }
    
    /**
     * set
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->property[$name] = $value;
    }
    
    
}