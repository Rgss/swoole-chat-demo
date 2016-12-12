<?php
/**
 * loader
 * 
 * @author zhang
 *
 */

class Loader {
    
    
    /**
     * class map
     * 
     */    
    protected static $map;

    /**
     * load service
     *
     * @param string $class            
     */
    public static function service($class) {
        if (isset(self::$map[$class])) {
            return true;
        }
        
        self::$map[$class] = 1;
        
        return require dirname(__FILE__) . '/../services/' . $class . '.php';
    }
    
    /**
     * import file
     *
     * @param string $class
     */
    public static function import($class) {
        if (isset(self::$map[$class])) {
            return true;
        }
    
        self::$map[$class] = 1;
    
        return require dirname(__FILE__) . '/' . $class . '.php';
    }
    

}