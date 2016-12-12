<?php
/**
 * sql builder
 *
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2016 Imp All rights reserved.
 * @version 	1.0
 * @link
 */

class Builder {
    
    
    /**
     * 当前db实例
     * 
     * @var Mysqli
     */
    protected $db = null;
    
    /**
     * field
     *
     * @var mixed
     */
    protected $bindField = null;
    
    /**
     * value
     *
     * @var mixed
     */
    protected $bindValue = null;
    
    /**
     * field = value
     * 
     * @var string
     */
    protected $bindFieldValue = null;
    
    /**
     * join
     * 
     * @var array
     */
    protected $bindJoin = null;
    
    
    /**
     * 初始化
     * 
     * @param Mysqli $db
     */
    public function __construct($db) {
        $this->db = $db;
    }
        
    /**
     * return $field
     * 
     * @return mixed
     */
    public function getBindField() {
        return $this->bindField;
    }
    
    /**
     * set bind field
     * 
     * @param mixed $field
     */
    public function setBindField($field) {
        $this->bindField = $field;
    }
    
    /**
     * return value
     * 
     * @return mixed
     */
    public function getBindValue() {
        return $this->bindValue;
    }
    
    /**
     * set bind value
     * 
     * @param mixed $value
     */
    public function setBindValue($value) {
        $this->bindValue = $value;
    }
    
    /**
     * return field-value
     * 
     * @return string
     */
    public function getBindFieldValue() {
        return $this->bindFieldValue;
    }
    
    /**
     * set bind field value
     * 
     * @param string $value
     */
    public function setBindFieldValue($value) {
        $this->bindFieldValue = $value;
    }
    
    /**
     * sql
     * 
     * @return string
     */
    public function query() {
        $sql = 'SELECT ' . $this->db->getColumn() . ' FROM ' . $this->getFullTableName() . $this->db->getJoin() . ' '
            . $this->db->getWhere() . ' ' . $this->db->getGroup() . ' ' . $this->db->getHaving() . ' ' 
            . $this->db->getOrder() . ' ' . $this->db->getLimit();
        
        return $sql;
    }
    
    /**
     * insert sql
     * 
     * @return string
     */
    public function insertQuery() {
        $sql = 'INSERT INTO '. $this->getFullTableName() .' ('. $this->getBindField() .') VALUES ('. $this->getBindValue() .')';
        return $sql;
    }
    
    /**
     * delay insert sql
     * 
     * @return string
     */
    public function delayInsertQuery() {
        $sql = 'INSERT DELAYED INTO '. $this->getFullTableName() .' ('. $this->getBindField() .') VALUES ('. $this->getBindValue() .')';
        return $sql;
    }
    
    /**
     * replace sql
     * 
     * @return string
     */
    public function replaceQuery() {
        $sql = 'REPLACE INTO '. $this->getFullTableName() .' ('. $this->getBindField() .') VALUES ('. $this->getBindValue() .')';
        return $sql;
    }
    
    /**
     * update sql
     * 
     * @return string
     */
    public function updateQuery() {
        $sql = 'UPDATE ' . $this->getFullTableName() . ' SET ' . $this->getBindFieldValue() . ' ' . $this->db->getWhere(). ' ' . $this->db->getHaving()  . ' ' . $this->db->getLimit();
        return $sql;
    }
    
    /**
     * delete sql
     * 
     * @return string
     */
    public function deleteQuery() {
        $sql = 'DELETE FROM ' . $this->getFullTableName() . $this->db->getWhere() . ' ' . $this->db->getHaving() . ' ' . $this->db->getLimit();
        return $sql;
    }
       
    /**
     * increment sql
     * 
     * @return string
     */
    public function incrementQuery() {
        $sql = 'UPDATE ' . $this->getFullTableName() . ' SET ' . $this->getBindField() . ' = '. $this->getBindField()  . ' + ' .  $this->getBindValue()  . ' ' . $this->db->getWhere() . ' ' . $this->db->getHaving() . ' ' . $this->db->getLimit();
        return $sql;
    }
    
    /**
     * decrement sql
     * 
     * @return string
     */
    public function decrementQuery() {
        $sql = 'UPDATE ' . $this->getFullTableName() . ' SET ' . $this->getBindField() . ' = ' . $this->getBindField()  . ' - ' .  $this->getBindValue()  . ' ' . $this->db->getWhere() . ' ' . $this->db->getHaving() . ' ' . $this->db->getLimit();
        return $sql;
    }
    
    /** 
     * page sql
     * 
     */
    public function pageQuery() {
        
    }
    
    /**
     * in where
     * 
     * @return string
     */
    public function whereInQuery() {
        return ' ' . $this->getBindField() . ' IN ('. $this->getBindValue() . ')';
    }
    
    /**
     * exists where
     * 
     * @return string
     */
    public function whereExistsQuery() {
        return ' EXISTS ('. $this->getBindValue() . ')';
    }
    
    /**
     * return full table name
     * 
     */
    public function getFullTableName() {
        return $this->db->getTable() . (!empty($this->db->getAlias()) ? ' AS ' . $this->db->getAlias() : '');
    }
    
    /**
     * 绑定字段和值
     * 
     */
    public function bindFieldAndValue($data) {
        $this->bindField = '';
        $this->bindValue = '';
        foreach ($data as $key => $val) {
            $this->bindField .= $this->parseField($key) . ',';
            $this->bindValue .= is_numeric($val) ? ($val . ',') : ("'" . $this->parseValue($val) . "',");
        }
        
        $this->bindField = trim($this->bindField, ',');
        $this->bindValue = trim($this->bindValue, ',');
    }
    
    /**
     * bind field value
     * 
     * @param array $data
     */
    public function bindFieldEqualValue($data) {
        $this->bindFieldValue = '';
        foreach ($data as $key => $val) {
            $this->bindFieldValue .= $this->parseField($key) . ' = ' . (is_numeric($val) ? ($val . ',') : ("'" . $this->parseValue($val) . "',"));
        }
        
        $this->bindFieldValue = trim($this->bindFieldValue, ',');
    }
    
    /**
     * bind value
     * 
     * @param array $data
     */
    public function bindValue($data) {
        if (is_string($data)) {
            $this->bindValue = $data;
            return;
        }
        
        $this->bindValue = '';
        foreach ($data as $key => $val) {
            $this->bindValue .= is_numeric($val) ? ($val . ',') : ("'" . $this->parseValue($val) . "',");
        }
        
        $this->bindValue = trim($this->bindValue, ',');
    }
    
    /**
     * join 
     * 
     * @param string $table
     * @param string $alias
     * @param string $mode
     */
    public function bindJoin($table, $mode, $alias) {
        $this->bindJoin[$table] = array(
            'table' => $table,
            'alias' => $alias,
            'mode'  => $mode,
            'on' => null,
        );
    }
    
    /**
     * on
     * 
     * @param string $table
     * @param string $first
     * @param string $mode
     * @param string $second
     * @param string $boolean
     */
    public function bindJoinOn($table, $first, $mode, $second, $boolean) {
        $this->bindJoin[$table]['on'] .= !empty($this->bindJoin[$table]['on']) ? 
            $boolean . ($first . " {$mode} " . $second) : (' ON ' . $first . " {$mode} " . $second);
    }
    
    /**
     * join query
     * 
     * @return string
     */
    public function joinQeury() {
        if (empty($this->bindJoin)) { return ' ';}
        
        $sql = '';
        foreach ($this->bindJoin as $table => $value) {
            $sql .= " {$value['mode']} JOIN `{$table}` {$value['alias']}";
            
            if (!empty($value['on'])) {
                $sql .= " " . $value['on'];
            }
        }
        
        return $sql;
    }
    
    /**
     * 解析字段
     * 
     * @param string $field
     * @return string
     */
    public function parseField($field) {
		return '`' . trim($field) . '`';
	}
    
	/**
	 * 过滤值
	 * 
	 * @param string $value
	 * @return string
	 */
    public function parseValue($value) {

        if (function_exists('mysql_real_escape_string') and is_resource($this->db->getCurrentLink())) {
            
            return $this->db->getCurrentLink()->real_escape_string($value);
            
        } elseif (function_exists('mysql_escape_string')) {
            
            return $this->db->getCurrentLink()->escape_string($value);
            
        } else {
            
            return addslashes($value);
        }

    }
    
}

