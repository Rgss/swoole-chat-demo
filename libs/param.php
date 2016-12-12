<?php
/**
 * params
 * 
 * @author zhang
 *
 */

class Param {
    
    /**
     * 
     * @var array
     */
    protected static $_var = [];

    /**
     * get
     * 
     * @param string $key
     */
    public static function get($key) {
        return isset(self::$_var[$key]) ? self::$_var[$key] : null;
    }
    
    /**
     * set
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value) {
        self::$_var[$key] = $value;
    }

    /**
     * set content
     *
     * @param mixed $content            
     */
    public static function setContent($content) {
        if (is_array($content)) {
            foreach ($content as $k => $v) {
                Param::set($k, $v);
            }
        } else {
            Param::set('content', $content);
        }
    }

    /**
     * get all
     * 
     */
    public static function all() {
        return self::$_var;
    }
    
}