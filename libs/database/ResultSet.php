<?php
/**
 * result set
 *
 * @author 		Imp <53404280@qq.com>
 * @copyright 	2015-2016 Imp All rights reserved.
 * @version 	1.0
 * @link
 */

class ResultSet {

    /**
     * return result as array
     *
     */
    public function toArray() {
        
        $data = array();
        
        $result = Connection::getConnector()->db()->getResult();
        foreach ($result as $k => $v) {
            if (is_array($v) || is_object($v)) {
                $data[$k] = $v->toArray();
            } else {
                $data[$k] = $v;
            }
        }
    
        return $data;
    }
    
    /**
     * return result as json
     *
     * @param string $option
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE) {
        return json_encode(Connection::getConnector()->db()->getResult(), $option);
    }

    /**
     * return the result as serialize
     *
     */
    public function toSerialize() {
        return serialize(Connection::getConnector()->db()->getResult());
    }
    
}
