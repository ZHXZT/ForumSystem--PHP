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
    skip('father_module.php','error','id传递失败');
}

$query="select * from xzt_son_module where father_module_id={$_GET['id']}";
$result=execute($link,$query);
if(mysqli_num_rows($result)){
    skip('father_module.php','error','该父板块下存在子板块，请先删除子板块！');
}


$query="delete from xzt_father_module where id={$_GET['id']}";
execute($link,$query);
if (mysqli_affected_rows($link)==1){
    skip('father_module.php','ok','删除成功！');
}else{
    skip('father_module.php','error','删除失败！');
}


