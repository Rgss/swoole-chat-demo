<?php
/**
 * generator
 * 
 * @author zhang
 * @date   2016-12-05
 *
 */

class Creator {
    
    
    /**
     * return token
     * 
     */
    public function genToken() {
        
        return $this->_genToken(16);
    }
    
    /**
     * generate token
     * 
     * @param number $num
     */
    private function _genToken($num = 16) {
        
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $len = strlen($str);
        $token = '';
        
        for ($i = 0; $i < $num; $i ++) {
            $rand = mt_rand(0, $len - 1);
            $token .= $str[$rand];
        }
  
        return $token;
    }
    
}

