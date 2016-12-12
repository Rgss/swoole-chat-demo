<?php
/**
 * Token manager
 * 
 * @author zhang
 * @date   2016-12-05
 *
 */

class Token {
    
    
    /**
     * current token
     * 
     * @var string
     */
    protected $token;
    
    
    /**
     * return token
     * 
     */
    public function getToken() {
        
        return $this->token;
    }
    
    /**
     * set token
     * 
     * @param string $token
     */
    public function setToken($token) {
        
        $this->token = $token;
    }
    
}

