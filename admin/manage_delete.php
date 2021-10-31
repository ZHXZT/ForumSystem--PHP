<?php
include_once '../inc/config.inc.php';
include_once '../inc/mysql.inc.php';
include_once '../inc/tool.inc.php';
$link=connect();
//验证后台是否登录
if (!is_manage_login($link)){
    header('Location:login.php');
    exit();
}

if (!isset($_GET['id'])||!is_numeric($_GET['id'])){
    skip('manage.php','error','id传递失败');
}

$query="delete from xzt_manage where id={$_GET['id']}";
execute($link,$query);
if (mysqli_affected_rows($link)==1){
    skip('manage.php','ok','删除成功！');
}else{
    skip('manage.php','error','删除失败！');
}


