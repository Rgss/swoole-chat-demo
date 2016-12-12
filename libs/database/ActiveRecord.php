<?php
/**
 * DB 对外统一接口
 *
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link
 */

class ActiveRecord {

    
	/**
	 * 数据表
	 * 
	 * @var string
	 */
	protected $table;
	
	/**
	 * model
	 * 
	 * @var object
	 */
	protected $model;
	
	/**
	 * 自动验证
	 * 
	 * @var array
	 */
	protected $validate = array();  // 自动验证定义
	
	/**
	 * 
	 * @var array
	 */
	protected $auto = array();  // 自动完成定义
	
	/**
	 * 对象属性， 对应数据库字段
	 * 
	 * @var array
	 */
	protected $property = array();
	
	/**
	 * primary key
	 * 
	 * @var string
	 */
	protected $primaryKey = 'id';
	
	/**
	 * column
	 * 
	 * @var string
	 */
	protected $column = '*';
	
	/**
	 * sql
	 * 
	 * @var string
	 */
	protected $sql;
	
	
	/**
     * 初始化
     * 
     * @param Connection $db
     * @param Model $model
     */
    public function __construct($db, $table, $alias = null) {
        
        $this->table = $table;
        
        $this->db = $db;
        
        $this->db->setTable($table);
        
        $this->db->setAlias($alias);
    }
    
    /**
     * return db connection
     * 
     */
    public function db() {
        return $this->db;
    }
    
    /**
     * return table name
     * 
     */
    public function getTable() {
        return $this->table;
    }
      
    /**
     * set table name
     *
     * @param string $name
     */
    public function table($name) {
        $this->table = $name;
        
        return $this;
    }
    
    /**
     * query
     *
     * @param string $sql
     */
    public function query($sql='') {
        if (empty($sql)) {
            return false;
        }
        
        return $this->db()->query($sql);
    }
    
    /**
     * get records
     *
     * @param string $sql
     */
    public function get() {
        return $this->db()->fetch();
    }
    
    /**
     * fetch record by sql
     *
     * @param string $sql
     */
    public function fetch($sql = null) {
        return $this->db()->fetch($sql);
    }

    /**
     * find one record
     *
     * @param nubmer $id
     */
    public function find($id, $column = array('*')) {
        $this->where($this->primaryKey, '=', $id);
        
        $this->select($column);
        
        return $this->db()->fetch();
    }
    
    /**
     * 获取多条记录
     *
     * @param string $sql
     * @return mixed >
     */
    public function all($column = array('*')) {
        $this->select($column);
        
        return $this->db()->fetchAll(null, null);
    }
    
    /**
     * 自增操作
     *
     * @param array $field
     * @param array $where
     * @return boolean
     */
    public function increment($field, $step = 1) {
        if (empty($this->db()->getWhere())) {
            $this->db()->setLimit(1);
        }
        return $this->db()->increment($field, $step);
    }
    
    /**
     * 自减操作
     *
     * @param array $field
     * @param array $where
     * @return boolean
     */
    public function decrement($field, $step = 1) {
        if (empty($this->db()->getWhere())) {
            $this->db()->setLimit(1);
        }
        return $this->db()->decrement($field, $step);
    }
    
    /**
     * page
     *
     * @param number $page
     * @param number $size
     * @param number $total
     * @param string $sql
     */
    public function page($page = 1, $size = 10, $total = 0, $sql = null) {
        return $this->db()->page($sql, $page, $size, $total);
    }
    
    /**
     * 添加记录
     *
     * @param array $data
     * @param string $lastInsertId
     * @return mixed
     */
    public function insert($data, $lastInsertId = false) {
        return $this->db()->insert($this->table, $data, $lastInsertId);
    }
    
    /**
     * 添加多条记录
     *
     * @param array $data
     * @return boolean
     */
    public function multiInsert($data) {
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
    public function update($data = array()) {
        $where = $this->db()->getWhere(false);
        $limit = $this->db()->getLimit();
    
        if (empty($where)) { return false;}
        $data = !empty($data) ? $data : $this->property;
        return $this->db()->update($this->table, $data);
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
        return $this->db()->fetch($sql);
    }
    
    /**
     * 删除记录
     *
     * @return boolean
     */
    public function delete($limit = 1) {
        return $this->db()->delete($this->table, $limit);
    }

    /**
     * 设置sql语句
     *
     * @param string $sql
     */
    public function setSql($sql = null) {
        $this->sql = $sql;
    }                       
    
    /**
     * return sql
     *
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }

    /**
     * sum
     * 
     * @param string $field
     * @param string $alias
     */
    public function sum($field, $alias = null) {
        $field = !empty($alias) ? "SUM({$field}) AS {$alias}" : "SUM({$field})";
        $this->select(array($field));
        
        return $this->get();
    }
    
    public function min() {
        
    }
    
    public function max() {
        
    }
    
    public function ave() {
        
    }
    
    /**
     * select column
     *
     * @param array $column
     * @return ActiveRecord
     */
    public function select($column = ['*']) {
        $this->db()->setColumn($column);
        
        return $this;
    }
          
    /**
     * set limit
     *
     * @param string $str
     * @return ActiveRecord
     */
    public function limit($str) {
        $this->db()->setLimit($str);
        
        return $this;
    }
       
    /**
     * set sql where
     *
     * @param array $data
     * @return ActiveRecord
     */
    public function where($data) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (!is_array($v)) {
                    $this->setWhere($k, '=', $v);
                } else {
                    $params = $this->parseWhere($v);
                    $this->setWhere($params[0], $params[1], $params[2], $params[3]);
                }
            }
        } else {
            $params = $this->parseWhere(func_get_args());
            if (count($params) == 1) {
                //$this->setWhere($params[0]);
                $this->db()->setWhere($where);
            } else {
                $this->setWhere($params[0], $params[1], $params[2], $params[3]);
            }
        }
        
        return $this;
    }
    
    /**
     * or
     * 
     * @param string $field
     * @param string $value
     * @param string $mode
     */
    public function whereOr($field, $mode, $value) {
        $this->where($field, $mode, $value, 'OR');
        
        return $this;
    }
    
    /**
     * in where
     * 
     * @param string $field
     * @param string $value
     * @param string $boolean
     */
    public function whereIn($field, $value, $boolean = 'AND') {
        $this->db()->setWhereIn($field, $value, $boolean);
        
        return $this;
    }
    
    /**
     * exists
     * 
     * @param string $where
     */
    public function whereExists($where) {
        $this->db()->setWhereExists($where, $boolean);
        
        return $this;
    }
    
    /**
     * parse where
     * 
     * @param array $data
     * @throws ImpException
     * @return array
     */
    public function parseWhere($data) {
        if (count($data) < 2) {
            throw new ImpException('sql where error');
        }
        
        $params = array();
        $params[0] = $data[0];
        $params[1] = isset($data[1]) ? $data[1] : '=';
        $params[2] = isset($data[2]) ? $data[2] : null;
        $params[3] = isset($data[3]) ? $data[3] : 'AND';
        
        return $params;
    }
    
    /**
     * set where
     *
     * @param string $field
     * @param string $value
     * @param string $mode
     * @param string $boolean
     */
    protected function setWhere($field, $mode = '=', $value = null, $boolean = 'AND') {
        $value = is_numeric($this->db()->getBuilder()->parseValue($value)) ? $this->db()->getBuilder()->parseValue($value) : "'". $this->db()->getBuilder()->parseValue($value) . "'";
        $where = $this->db()->getBuilder()->parseField($field) . ' ' . $mode . ' ' . $value;
        $where = !empty($this->db()->getWhere()) ? ($boolean . $where) : $where;
    
        $this->db()->setWhere($where);
        
        return $this;
    }
    
    /**
     * join 
     * 
     * @param string $table
     * @param string $alias
     */
    public function join($table, $mode = NULL, $alias = null) {
        $this->db()->join($table, $mode, $alias);
        
        return $this;
    }
    
    /**
     * left join
     * 
     * @param string $table
     * @param string $alias
     * @return ActiveRecord
     */
    public function leftJoin($table, $alias = null) {
        $this->db()->join($table, 'LEFT', $alias);
        
        return $this;
    }
    
    /**
     * right join
     *
     * @param string $table
     * @param string $alias
     * @return ActiveRecord
     */
    public function rightJoin($table, $alias = null) {
        $this->db()->join($table, 'RIGHT', $alias);
        
        return $this;
    }
    
    /**
     * cross join
     *
     * @param string $table
     * @param string $alias
     * @return ActiveRecord
     */
    public function crossJoin($table, $alias = null) {
        $this->db()->join($table, 'CROSS', $alias);
        
        return $this;
    }
    
    /**
     * join on
     *
     * @param string $table
     * @param string $alias
     * @return ActiveRecord
     */
    public function on($table, $first, $mode, $second, $boolean = 'AND') {
        $this->db()->on($table, $first, $mode, $second, $boolean);
        
        return $this;
    }

    /**
     * set having
     *
     * @param string $str
     * @return ActiveRecord
     */
    public function having($str) {
        $this->db()->setHaving($str);
        
        return $this;
    }
    
    /**
     * set group
     *
     * @param string $str
     * @return ActiveRecord
     */
    public function groupBy($str) {
        $this->db()->setGroup($str);
        
        return $this;
    }

    /**
     * set order
     *
     * @param string $str
     * @return ActiveRecord
     */
    public function orderBy($str) {
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
     * commit
     *
     */
    public function commit() {
        $this->db()->commit();
    }
    
    /**
     * rollback
     *
     */
    public function rollback() {
        $this->db()->rollback();
    }
        
    /**
     * set result type
     * 
     * @param string $type
     */
    public function type($type = 'object') {
        $this->db()->setResultType($type);
    }

}
