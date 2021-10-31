<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
$link=connect();
if (!is_login($link)){
    skip('index.php','error','你没有登录！');
}
setcookie('xzt[name]','',time()-3600);
setcookie('xzt[pw]','',time()-3600);
skip('index.php','ok','退出成功！');
