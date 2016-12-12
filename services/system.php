<?php
/**
 * system service
 * 
 * @author zhang
 * @date   2016-12-11
 */

class System {
 
            
    /**
     * index
     * 
     */
    public function index() {
     
        return array(
            'status' => 1,
            'type' => CMD::CMD_SYSTEM,
            'content' => 'ok',
            'time' => time(),
            'date' => date('Y-m-d H:i:s'),
        );
    }
    
    
    /**
     * return online users
     * 
     */
    public function online() {
        
        $users = array();
        $userService = Service::instance()->get('user');
        $online = $userService->getOnline();
        foreach ($online as $key => $value) {
             $users[] = $userService->get($value['uid']);     
        }
        
        return array(
            'status' => 1,
            'type' => CMD::CMD_ONLINE,
            'content' => array(
                'list' => $users,
            ),
            'time' => time(),
            'date' => date('Y-m-d H:i:s'),
        );
    }
 

}
