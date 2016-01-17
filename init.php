<?php
define('ROOT_PATH', dirname(__FILE__));

require ROOT_PATH . '/config.php';
require ROOT_PATH . '/libs/common.php';
require ROOT_PATH . '/libs/WPCF.class.php';
require ROOT_PATH . '/libs/Smarty.class.php';

class DB extends mysqli {
    function __construct($host, $user, $pass, $db) {
        parent::__construct($host, $user, $pass, $db);
        if ($this->connect_error) {
            die('Connect Error (' . $this->connect_errno . ') ' . $this->connect_error);
        }
    }

    function clean($str) {
        return $this->real_escape_string($str);
    }
}

$mysqli = new DB(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DBNM);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$smarty = new Smarty;
$smarty->caching = false;
$smarty->setTemplateDir(ROOT_PATH . '/templates/');
$smarty->setCompileDir(ROOT_PATH . '/templates_c/');

$wpcf = new WPCF;

session_start();
