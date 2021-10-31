<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
$link=connect();
//var_dump(is_login($link));exit();
if (is_login($link)){
    skip('index.php','error','你已经登录，请不要重复注册！');
}
if (isset($_POST['submit'])){
    if (empty($_POST['name'])){
        skip('register.php','error','用户名不得为空！');
    }
    if (mb_strlen($_POST['name'])>32){
        skip('register.php','error','用户名过长！');
    }
    if (mb_strlen($_POST['pw'])<6){
        skip('register.php','error','密码不得小于6位！');
    }
    if ($_POST['pw']!=$_POST['confirm_pw']){
        skip('register.php','error','两次密码不一致！');
    }
    if (strtolower($_POST['vcode'])!=strtolower($_SESSION['vcode'])){
        skip('register.php','error','验证码输入错误！');
    }
    $_POST=escape($link,$_POST);
    $query="select * from xzt_member where name='{$_POST['name']}'";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)){
        skip('register.php','error','该用户名已经被注册！');
    }

    $query="insert into xzt_member(name,pw,register_time) values ('{$_POST['name']}',md5('{$_POST['pw']}'),now())";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){

        setcookie('xzt[name]',$_POST['name']);
        setcookie('xzt[pw]',md5($_POST['pw']));
        skip('index.php','ok','注册成功！');
    }else{
        skip('register.php','error','注册失败！请重试');
    }

}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" type="text/css" href="style/public.css" />
    <link rel="stylesheet" type="text/css" href="style/register.css" />
</head>
<body>
<div class="header_wrap">
    <div id="header" class="auto">
        <div class="logo">XZT</div>
        <div class="nav">
            <a class="hover" href="index.php">首页</a>
        </div>
        <div class="serarch">
            <form>
                <input class="keyword" type="text" name="keyword" placeholder="搜索其实很简单" />
                <input class="submit" type="submit" name="submit" value="" />
            </form>
        </div>
        <div class="login">
            <a href="login.php">登录</a>&nbsp;
            <a href="register.php">注册</a>
        </div>
    </div>
</div>
<div style="margin-top:80px;"></div>
<div id="register" class="auto">
    <h2>欢迎注册成为 xzt会员</h2>
    <form method="post">
        <label>用户名：<input type="text" name="name"  /><span>*用户名不得为空且不得超过32字符</span></label>
        <label>密码：<input type="password" name="pw" /><span>*密码不得少于6位</span></label>
        <label>确认密码：<input type="password" name="confirm_pw"/><span>*请再次输入密码</span></label>
        <label>验证码：<input type="text" name="vcode" /><span>*请输入下方验证码</span></label>
        <img class="vcode" src="show_vcode.php" />
        <div style="clear:both;"></div>
        <input class="btn" type="submit" value="确定注册" name="submit" />
    </form>
</div>
<div id="footer" class="auto">
    <div class="bottom">
        <a>xzt</a>
    </div>
    <div class="copyright">Powered by xzt ©2015 xzt.com</div>
</div>
</body>
</html>