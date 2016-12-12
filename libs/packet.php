<?php
/**
 * Packet
 * 
 * @author zhang
 */

class Packet {
        
    /**
     * pack
     * 
     * @param string $str
     */
    public static function pack($str) {
        return pack('', $str);
    }
    
    /**
     * unpack
     * 
     * @param string $str
     */
    public static function unpack($str) {
        return unpack('N', $str);
    }
    
    /**
     * encode
     * 
     * @param array $data
     */
    public static function encode($data) {
        return json_encode($data);
    }
    
    /**
     * decode
     * 
     * @param string $data
     */
    public static function decode($data) {
        return json_decode($data, true);
    } 
    
}
