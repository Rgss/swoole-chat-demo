<?php
/**
 * talk service
 * 
 * @author zhang
 *
 */

class Talk {
    
    
    /**
     * construct
     * 
     */
    public function __construct() {
        error_log('talk __construct');
    }
        
    /**
     * all
     * 
     */
    public function one() {
        echo "[service] talk one\n";
        error_log('[service] talk one');
        
        // error_log("serv: " . print_r($this->server, 1) );
        $uid = Param::get('uid');
        $message = Param::get('message');
        
        Loader::service('user');
        $userService = Service::instance()->get('user');
        $uid = $userService->getCurrentUid();
        $user = $userService->get($uid);
        
        return array(
            'status' => 1,
            'type' => CMD::CMD_TALK,
            'uid' => $uid,
            'user' => $user,
            'content' => $message,
            'time' => time(),
            'date' => date('Y-m-d H:i:s'),
        );
    }
    
    /**
     * to one
     * 
     */
    public function all() {
        echo "[service] talk all\n";
        error_log('[service] talk all');
        
        $uid = Param::get('uid');
        $message = Param::get('message');
        
        Loader::service('user');
        $userService = Service::instance()->get('user');
        $uid = $userService->getCurrentUid();
        $user = $userService->get($uid);
        
        $data = array(
            'status' => 1,
            'type' => CMD::CMD_TALK_ALL,
            'uid' => $uid,
            'user' => $user,
            'content' => $message,
            'time' => time(),
            'date' => date('Y-m-d H:i:s'),
        );
        
        $server = Service::instance()->getMainServer();
        $server->broadcast($server->getServer(), $server->connections(null, false), $data, $server->getCurrentFD());
        
        return array(
            'status' => 1,
            'type' => CMD::CMD_TALK_ALL,
            'uid' => $uid,
            'user' => $user,
            'content' => $message,
        );
    }

}
