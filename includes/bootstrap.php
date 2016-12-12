<?php
/**
 * bootstrap
 * 
 * @author zhang
 * 
 */


define('ROOT_PATH', dirname(__FILE__) . '/../');

require ROOT_PATH . 'includes/config.php';
require ROOT_PATH . 'libs/cmd.php';
require ROOT_PATH . 'libs/loader.php';
require ROOT_PATH . 'libs/packet.php';
require ROOT_PATH . 'libs/param.php';
require ROOT_PATH . 'libs/service.php';

require ROOT_PATH . 'libs/database/DB.php';
require ROOT_PATH . 'libs/database/Connection.php';

