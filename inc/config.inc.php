<?php
date_default_timezone_set('Asia/Shanghai');
session_start();
header('Content-Type:text/html;charset:utf8');
//检测php版本
if (version_compare(PHP_VERSION,'5.4.0')<0){
    exit('你的php版本为'.PHP_VERSION.'！程序要求php版本不得低于5.4.0');
}
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_DATABASE','xztbbs');
define('DB_PORT','3306');
//我们的项目（程序），在服务器上的绝对路径
define('SA_PATH',dirname(dirname(__FILE__)));
//我们的项目在web根目录下面的位置（哪个目录里面）
define('SUB_URL',str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\','/',SA_PATH)).'/');


