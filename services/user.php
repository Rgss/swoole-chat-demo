<?php

class User {
    
	
    /**
     * max user id
     * 
     * @var number
     */
    static $maxUserID = 0;
	
    /**
     * users db
     * 
     * @var array
     */
	protected $users = [];
	
	/**
	 * usernames list
	 * 
	 * @var array
	 */
	protected $usernames = [];
	
	/**
	 * online user
	 * 
	 * @var array<Token>
	 */
	protected $online = [];
	
	
	/**
	 * construct
	 *
	 */
	public function __construct() {
	    error_log('user __construct');
	}
	
	/**
	 * login
	 * 
	 */
	public function login() {
	    
	    $username = Param::get('username');
	    $password = Param::get('password');
	    if (empty($username) || empty($password)) {
	        return array(
	            'status' => 0,
	            'content' => '帐号密码不能为空',
	        );
	    }
	    
	    if (! $this->isExistsByUsername($username)) {
	        return array(
	            'status' => 0,
	            'content' => '帐号不存在',
	        );
	    }
	    
	    $uid = $this->getUidByUsername($username);
	    $user = $this->get($uid);
	    if (! $user) {
	        return array(
	            'status' => 0,
	            'content' => '帐号不存在',
	        );
	    }
	    
	    Loader::service('creator');
	    $generator = Service::instance()->get('creator');
	    $token = $generator->genToken();
	    
	    error_log("login uid:" . $uid . ' token:' . $token . ' username:' . $username);
	    
	    return array(
	        'status' => 1,
	        'uid' => $uid,
	        'user' => $user,
	        'type' => CMD::CMD_LOGIN,
	        'token' => $token,
	        'content' => '登录成功',
	    );
	}
		
	
	/**
	 * register
	 * 
	 */
	public function register() {
        $username = Param::get('username');
	    $password = Param::get('password');
	    $logo     = Param::get('logo');
	    $sex = mt_rand(0, 1);
	    if (empty($username) || empty($password)) {
	        return array(
	            'status' => 0,
	            'content' => '帐号密码不能为空',
	        );
	    }
	    
	    if ($this->isExistsByUsername($username)) {
	        return array(
	            'status' => 0,
	            'content' => '帐号已存在',
	        );
	    }
		
	    self::$maxUserID  += 1;
	    $uid = self::$maxUserID;
	    $user = array(
	        'uid' => $uid,
	        'username' => $username,
	        'password' => $password,
	        'sex' => $sex ? '男' : '女',
	        'logo' => $logo,
	        'create' => time(),
	        'modify' => time(),
	    );
	    
	    // register
	    $this->_makeRegister($user);
	    
	    // login
	    Loader::service('creator');
	    $generator = Service::instance()->get('creator');
	    $token = $generator->genToken();
	    $this->_makeOnline($token, $uid, Service::instance()->getMainServer()->getCurrentFD());
	    
	    error_log("register uid:" . $uid . ' token:' . $token . ' username:' . $username);
	    
	    return array(
	        'status' => 1,
	        'uid' => $uid,
	        'user' => $user,
	        'type' => CMD::CMD_REGISTER,
	        'token' => $token,
	        'content' => '登录成功',
	    );
	}
	
	
	/**
	 * get user
	 * 
	 * @param number $uid
	 */
	public function get($uid) {
	    return isset($this->users[$uid]) ? $this->users[$uid] : null;
	}
	
	/**
	 * get user
	 * 
	 * @param string $username
	 */
	public function getUserByUsername($username) {
	    $user = DB::table('user')->where('username', '=', $username)->get();
	     
	    return $user;
	}
	
	/**
	 * get user by token
	 * 
	 * @param string $token
	 */
	public function getUserByToken($token) {
	    
	}
	
	public function setUserByToken($token, $user) {
	     
	}
	
	/**
	 * uid
	 * 
	 * @param string $username
	 */
	public function getUidByUsername($username) {
	    return isset($this->usernames[$username]) ? $this->usernames[$username] : 0;
	}
	
	/**
	 * check username
	 * 
	 * @param string $username
	 * @return boolean
	 */
	public function isExistsByUsername($username) {
	    $username = md5($username);
	    return isset($this->usernames[$username]) ? true : false;
	}

	/**
	 * mark online
	 * 
	 * @param string $token
	 * @param number $uid
	 */
	public function _makeOnline($token, $uid, $fd) {
	    $this->online[$token] = array(
	        'uid' => $uid,
	        'fd' => $fd,
	    );
	}
	
	/**
	 * register
	 * 
	 * @param User $user
	 */
	public function _makeRegister($user) {
	    $this->users[$user['uid']] = $user;
	    
	    $this->_makeUsername($user['username'], $user['uid']);
	}
	
	/**
	 * make username to uid
	 * 
	 * @param string $username
	 * @param number $uid
	 */
	public function _makeUsername($username, $uid) {
	    $username = md5($username);
	    $this->usernames[$username] = $uid;
	}
	
	/**
	 * return uid
	 * 
	 */
	public function getCurrentUid() {
	    Loader::service('token');
	    $token = Service::instance()->get('token')->getToken();
	    $uid = isset($this->online[$token]) ? $this->online[$token]['uid'] : 0;
	    
	    return $uid;
	}
	
    /**
     * return online
     * 
     * @return array<Token>
     */
	public function getOnline() {
	    return $this->online;
	}
}