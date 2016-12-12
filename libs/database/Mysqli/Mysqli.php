<?php
/**
 * Mysqli
 * 
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link	
 */

require './MysqliBase.php';

class Mysql extends MysqliBase {
	
        
	/**
	 * construct
	 * 
	 * @param array $config
	 */
	public function __construct($config = array()) {
		if (!empty($config)) {
			$this->_config = $config;
		}
		$this->connect($config['host'], $config['port'], $config['dbname'], $config['username'], $config['password']);
	}
	
	/**
	 * connect
	 * 
	 * @param string $host
	 * @param number $port
	 * @param string $database
	 * @param string $username
	 * @param string $password
	 */
	public function connect($host, $port, $database, $username, $password) {
		$this->_link = new Mysqli($host, $username, $password, $database, $port);
		if ($this->_link->connect_error) {
            die('Mysql connect error. error info: ' . $this->_link->connect_error);
        }
        
        $this->setDatabaseName($database);
        
		$this->setCharset();
	}
	
	/**
	 * set charset
	 * 
	 */
	public function setCharset() {
		$this->_link->set_charset($this->_config['charset']);
	}
	
	/**
	 * set database name
	 * 
	 * @param string $name
	 */
	public function setDatabaseName($name) {
	    $this->_dbname = $name;
	}
	
	/**
	 * select database
	 * 
	 * @param string $database
	 */
	public function selectDB($database) {
		return $this->_link->select_db($database);
	}
		
	/**
	 * return current link
	 * 
	 * @return resource
	 */
	public function getCurrentLink() {
	    return $this->_link;
	}
	
	/**
	 * query sql
	 * 
	 * @param string $sql
	 */
	public function query($sql) {
	    $this->clearCondition();
	    
        if (empty($sql)) {
            CError::show("query sql is empty.");
            die();
        }
        
        $this->logSqls($sql);

        try {
            $this->_query = $this->_link->query($sql);
            if ($this->_query === false) {
                $this->transactionCommit = false;
                CError::show($this->getError() . ' <br/>SQL: ' . $sql);
                return;
            }
            
        } catch (Exception $e) {
            $this->transactionCommit = false;
            CError::show($this->getError() . ' <br/>SQL: ' . $sql);
            return;
        }
                
        return $this->_query;
    }

    /**
     * return table
     *
     * @param string $table            
     */
    public function table($table) {
        return $this->_table = false === strpos($table, ' ') ? ('`' . $this->_tablePrefix . $table . '`') : ($this->_tablePrefix . $table);
    }
    
    /**
     * set table
     *
     * @param string $table
     */
    public function setTable($table) {
        return $this->_table = false === strpos($table, ' ') ? ('`' . $this->_tablePrefix . $table . '`') : ($this->_tablePrefix . $table);
    }
    
    /**
     * set alias 
     * 
     * @param string $alias
     */
    public function setAlias($alias) {
        $this->_alias = $alias;
    }
    
    /**
     * return alias
     * 
     * @return string
     */
    public function getAlias() {
        return $this->_alias;
    }
    
    /**
     * return table
     * 
     * @return string
     */
    public function getTable() {
        return $this->_table;
    }
    
    /**
     * column
     * 
     * @return string
     */
    public function getColumn() {
        return $this->_column;
    }
    
    /**
     * select column
     *
     * @param array $column
     */
    public function setColumn($column = ['*']) {
        $column_str = '';
        foreach($column as $v) {
            $column_str .= ($v == '*' ? '*' : ('`' . $v . '`,'));
        }

        $this->_column = trim($column_str , ',');
    }

    /**
     * insert
     * 
     * @param array $data
     * @param boolean $lastInsertId
     * @return mixed
     */
    public function insert($table, $data, $lastInsertId = false) {
        $this->setTable($table);
        
        $this->getBuilder()->bindFieldAndValue($data);
        
        $res = $this->query($this->getBuilder()->insertQuery());
        
        return $lastInsertId ? $this->lastInsertId() : $res;
    }
    
    /**
     * delayed insert
     * 
     * @param array $data
     * @return boolean
     */
    public function delayedInsert($table, $data) {
        $this->setTable($table);
        
        $this->getBuilder()->bindFieldAndValue($data);
        
        return $this->query($this->getBuilder()->delayInsertQuery());
    }
    
    /**
     * replace insert
     * 
     * @param array $data
     * @return mixed
     */
    public function replace($table, $data, $lastInsertId = false) {
        $this->setTable($table);
        
        $this->getBuilder()->bindFieldAndValue($data);
        
        $res = $this->query($this->getBuilder()->replaceQuery());
        
        return $lastInsertId ? $this->lastInsertId() : $res;
    }
	
	/**
	 * delete
	 * 
	 * @param type $limit
	 * @return type
	 */
	public function delete($table, $where = null, $limit = 1) {
	    $this->setTable($table);
	    
	    $this->setWhere($where);
	    
        return $this->query($this->getBuilder()->deleteQuery());
    }
	
	/**
	 * update
	 * 
	 * @param type $data
	 * @param string $where
	 * @return type
	 */
	public function update($table, $data, $where = null) {
	    $this->setTable($table);
	    
	    $this->setWhere($where);
	    
        $this->getBuilder()->bindFieldEqualValue($data);

        return $this->query($this->getBuilder()->updateQuery());
    }
	
	/**
	 * increment
	 * 
	 * @param string $field
	 * @param number $step
	 * @return boolean
	 */
	public function increment($field, $step = 1) {
	    $this->getBuilder()->bindFieldAndValue(array($field => $step));
	    
        return $this->query($this->getBuilder()->incrementQuery());
    }
    
    /**
     * decrement
     *
     * @param string $field
     * @param number $step
     * @return boolean
     */
    public function decrement($field, $step = 1) {
        $this->getBuilder()->bindFieldAndValue(array($field => $step));
        
        return $this->query($this->getBuilder()->decrementQuery());
    }
	
	/**
	 * return one record
	 * 
	 * @param string $sql
	 * @param number $type MYSQLI_ASSOC, MYSQLI_NUM, or MYSQLI_BOTH.
	 */
	public function fetch($sql = null, $type = MYSQLI_ASSOC) {
	    $sql = !empty($sql) ? $sql : $this->getBuilder()->query();
	    
        $this->query($sql);
        
        return $this->resultSet = $this->_fetchArray($type);
    }
	
	/**
	 * return multi record
	 * 
	 * @param string $sql
	 * @param string $index_key
	 * @return type
	 */
	public function fetchAll($sql = null, $index_key = null, $type = MYSQLI_ASSOC) {
	    $sql = !empty($sql) ? $sql : $this->getBuilder()->query();
        $this->query($sql);
        $this->resultSet = array();
        while ($row = $this->_fetchArray($type)) {
            if (! empty($index_key)) {
                $this->resultSet[$row['id']] = $row;
            } else {
                $this->resultSet[] = $row;
            }
        }
        return $this->resultSet;
    }
    
    /**
     * fetch records
     *
     * @param type $type
     * @return type
     */
    protected function _fetchArray($type = MYSQLI_ASSOC) {
        $result_type = $this->getResultType();
        if ($result_type == 'object') {
            return $this->_query->fetch_object(); // resultSet
        } elseif ($result_type == 'array') {
            return $this->_query->fetch_array($type);
        } else {
            throw new Exception('result type error');
        }
    }
    
    /**
     * return result set
     * 
     */
    public function getResult() {
        return $this->resultSet;
    }
    
    /**
     * set result type
     * 
     * @param string $type
     */
    public function setResultType($type = 'array') {
        $this->resultType = $type;
    }
	
    /**
     * return the result type
     * 
     */
    public function getResultType() {
        return $this->resultType;
    }
    
	/**
	 * return field of table
	 * 
	 * @param string $sql
	 * @return array
	 */
	public function fetchField($sql) {
		$fields = $this->query($sql)->fetch_field();
		return (array)$fields;
	}
	
	/**
	 * return field of table
	 * 
	 * @param string $sql
	 * @return array
	 */
	public function fetchFields($sql, $field_name = false) {
		$fields = $this->query($sql)->fetch_fields();
		$return_fields = array();
		foreach ($fields as $k => $v) {
			if ($field_name) {
				$return_fields[$k] = $v->name;
			} else {
				$return_fields[$k] = (array)$v;
			}
		}
		return $return_fields;
	}
	
	/**
	 * page
	 * 
	 * @param string $sql
	 * @param number $page
	 * @param number $size
	 * @param number $total
	 * @return mixed
	 */
    public function page($sql, $page = 1, $size = 10, $total=0) {
        $sql = !empty($sql) ? $sql : $this->getBuilder()->query();
        
        $page = intval($page);
        $size = intval($size);
        $total = intval($total);
        if ($page <= 1) { $page = 1;}
        if ($size <= 0) { $size = 1;}
		if ($total <= 0) {
            $totalSql = preg_replace('{select\s+(.*?)\s+from}i', 'select count(1) as `total` from ', $sql);
            $totalSql = preg_replace('{order\s+by\s+\w+(\s+(asc|desc))?(?:\s?(,)\s?\w+\s+(asc|desc)) {0,}}i', '', $totalSql);
            $result = $this->fetch($totalSql);
            $total = is_object($result) ? $result->total : $result['total'];
        }
        
        $pageInfo = array(
            'page' => $page, 'totalPage' => 0, 'size' => $size, 'total' => 0
        );
        
		if ($total < 1) {
            return array('list' => array(), 'pageinfo' => $pageInfo);
        }
        
		// 根据页大小和当前总行数获取最大页数
		$totalPage = ceil($total / $size);
		if ($page > $totalPage) { 
		    $page = $totalPage;
		    $pageInfo['page'] = $page;
		}
        
		$sql = $sql . ' LIMIT ' . (($page - 1) * $size) . ',' . $size;
        $data = $this->fetchAll($sql);
        if (!empty($data)) {
            $pageInfo['totalPage'] = $totalPage;
            $pageInfo['total'] = $total;
            
            return array('list' => $data,'pageinfo' => $pageInfo);
        }
            
        return array('list' => array(), 'pageinfo' => $pageInfo);
	}
	
	/**
	 * lastInsertId
	 * 
	 * @return type
	 */
	public function lastInsertId () {
		return $this->_link->insert_id; // select last_insert_id()
	}
	
	/**
	 * affected rows
	 * 
	 * @return type
	 */
	public function affectedRows() {
        return $this->_link->affected_rows;
    }
    
    /**
     * beginTransaction
     * 
     * @param boolean $mode
     */
    public function beginTransaction($mode = false) {
        $this->_link->autocommit($mode);
    }
    
    /**
     * commit
     * 
     */
    public function commit() {
        
        // rollback
        if ($this->transactionCommit === false) {
            $this->rollback();
            return;
        }
        
        $this->_link->commit();
        $this->_link->autocommit(true);
    }
    
    /**
     * rollback
     * 
     */
    public function rollback() {
        $this->_link->rollback();
        $this->_link->autocommit(true);
    }
	
	/**
	 * return all sqls
	 * 
	 * @return type
	 */
	public function getSqls() {
		return self::$_sqls;
	}
	
	/**
	 * record sql
	 * 
	 * @param unknown $sql
	 */
	public function logSqls($sql) {
	    self::$_sqls[] = $sql;
	}
		
	/**
	 * return where
	 *
	 * @return string
	 */
	public function getWhere($flag = true) {
		return (!empty($this->_where) && $flag) ? 'WHERE ' . $this->_where : $this->_where;
	}
		
	/**
	 * set where
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @param string $mode
	 * @param string $boolean
	 */
	public function setWhere2($field, $value, $mode = '=', $boolean = 'AND') {
	    $where = $this->getBuilder()->parseField($field) . ' ' . $mode . ' ' . $this->getBuilder()->parseValue($value);
	    $this->_where .= !empty($this->_where) ? ($boolean . $where) : $where;       
	}
	
	/**
	 * where
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param string $mode
	 * @param string $boolean
	 */
	public function setWhere($where) {
	    $this->_where .= $where;
	}
	
	/**
	 * in
	 * 
	 * @param string $field
	 * @param array $data
	 */
	public function setWhereIn($field, $data, $boolean = 'AND') {
	    $this->getBuilder()->bindValue($data);
	    
	    $this->getBuilder()->setBindField($field);
	    
	    $where = $this->getBuilder()->whereInQuery();
	    
	    $this->_where .= !empty($this->_where) ? ($boolean . $where) : $where;
	}
	
	/**
	 * exists
	 *
	 * @param string $field
	 * @param array $data
	 */
	public function setWhereExists($data, $boolean = 'AND') {
	    $this->getBuilder()->bindValue($data);
	          
	    $where = $this->getBuilder()->whereExistsQuery();
	     
	    $this->_where .= !empty($this->_where) ? ($boolean . $where) : $where;
	}
	
	/**
	 * return join
	 * 
	 * @return string
	 */
	public function getJoin() {
	    return $this->_join = $this->getBuilder()->joinQeury();
	}
	
	/**
	 * join
	 * 
	 * @param string $table
	 * @param string $alias
	 * @param string $mode
	 */
	public function join($table, $mode = 'INNER', $alias = null) {
	    $this->getBuilder()->bindJoin($table, $mode, $alias);
	}
	
	/**
	 * join where
	 * 
	 * @param string $table
	 * @param string $first
	 * @param string $mode
	 * @param string $second
	 * @param string $boolean
	 */
    public function on($table, $first, $mode, $second, $boolean = 'AND') {
        $this->getBuilder()->bindJoinOn($table, $first, $mode, $second, $boolean);
    }

	/**
	 * return limit
	 *
	 * @return string
	 */
	public function getLimit() {
		return $this->_limit;
	}
	
	/**
	 * set limit
	 * 
	 * @param string $field
	 */
	public function setLimit($limit) {
	    $this->_limit = !empty($limit) ? 'LIMIT '. $limit : '';
	}

	/**
	 * return group
	 *
	 * @return string
	 */
	public function getGroup() {
		return $this->_group;
	}
	
	/**
	 * set group
	 * 
	 * @param string $group
	 */
	public function setGroup($group) {
		$this->_group = 'GROUP BY ' . $group; 
	}

	/**
	 * return order
	 *
	 * @return string
	 */
	public function getOrder() {
		return $this->_order;
	}
	
	/**
	 * set order
	 * 
	 * @param mixed $field
	 */
	public function setOrder($data) {
		$order = 'ORDER BY ';
		if (is_array($data)) {
    		foreach ($data as $k => $v) {
    		    if (strtolower($v) == 'desc') {
    		        $order .= "`{$k}` DESC,";
    		    } else {
    		        $order .= "`{$k}` ASC,";
    		    }
    		}
    		
    		$order = trim($order, ',');
		} else {
		    $order .= $data;
		}
		
		$this->_order = $order;
	}

	/**
	 * return having
	 *
	 * @return string
	 */
	public function getHaving() {
		return $this->_having;
	}
	
	/**
	 * set having
	 * 
	 * @param string $field
	 */
	public function setHaving($field) {
		$this->_having = 'HAVING '. $field;
	}
	
	/**
	 * return errorno
	 * 
	 */
	public function getErrorno() {
	    return $this->_link->errno;
	}
	
	/**
	 * return error
	 * 
	 * @return string
	 */
	public function getError() {
		return $this->_link->error;
	}
	
	/**
	 * return error list
	 * 
	 * @return array
	 */
	public function getErrorList() {
		return $this->_link->error_list;
	}
	
	/**
	 * return mysql stat
	 * 
	 */
	public function getStat() {
	   return $this->_link->stat; 
	}
	
	/**
	 * return mysql version
	 * 
	 */
	public function getVersion() {
	    return $this->_link->server_version;
	}
	
	/**
	 * sql builder
	 * 
	 */
	public function getBuilder() {
	   if ($this->_builder === null) {
	       $this->_builder = new Builder($this);
	   } 
	   
	   return $this->_builder;
	}
	
	/**
	 * clear sql condition
	 *
	 */
	public function clearCondition() {
	    $this->_column = '*';
	    $this->_where = null;
	    $this->_alias = null;
	    $this->_limit = null;
	    $this->_group = null;
	    $this->_order = null;
	    $this->_having = null;
	    $this->_join = null;
	    $this->_leftJoin = null;
	    $this->_rightJoin = null;
	    $this->transactionCommit = true;
	    $this->resultType = 'object';
	    $this->resultSet = null;
	}
	
}
