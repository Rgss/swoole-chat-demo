<?php
/**
 * 命令id
 *
 * @author zhang
 * 
 */

class CMD {
	
    /**
     * system
     * @var number
     */
    const CMD_SYSTEM = 1000;
    

	/**
	 * login
	 * @var number
	 */
	const CMD_LOGIN = 1001;

	/**
	 * reg
	 * @var number
	 */
	const CMD_REGISTER = 1002;

	/**
	 * talk
	 * @var number
	 */
	const CMD_TALK = 2001;
    	
    /**
     * to all
     * @var number
     */
    const CMD_TALK_ALL = 2002;

    /**
     * task
     * @var number
     */
	const CMD_TASK = 3000;	
	
	/**
	 * online
	 * @var number
	 */
	const CMD_ONLINE = 4001;
	
	/**
	 * 命令对应的service配置
	 * 
	 * @var type 
	 */
	static $config = array(
	    
	    1000 => array(
			'service' => 'system',
			'action'  => 'index',
		),
		
		1001 => array(
			'service' => 'user',
			'action'  => 'login',
		),
		
		1002 => array(
			'service' => 'user',
			'action'  => 'register',
		),
		
		2001 => array(
			'service' => 'talk',
			'action'  => 'one',
		),
		
		2002 => array(
			'service' => 'talk',
			'action'  => 'all',
		),
		
	    
	    4001 => array(
	        'service' => 'system',
	        'action'  => 'online',
	    ),
		
	);
	
	
}
