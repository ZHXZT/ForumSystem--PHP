<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
$link=connect();
if (is_login($link)){
    skip('index.php','error','你已经登录请不要重复登录！');
}
if (isset($_POST['submit'])){
    if (empty($_POST['name'])){
        skip('login.php','error','用户名不得为空！');
    }
    if (empty($_POST['pw'])){
        skip('login.php','error','密码不得为空！');
    }
    if (strtolower($_POST['vcode'])!=strtolower($_SESSION['vcode'])){
        skip('login.php','error','验证码输入错误！');
    }
    if (empty($_POST['time'])||is_numeric($_POST['time'])||$_POST['time']>2592000){
        $_POST['time']=2592000;
    }
    escape($link,$_POST);
    $query="select * from xzt_member where name='{$_POST['name']}' and pw=md5('{$_POST['pw']}')";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)==1){
        setcookie('xzt[name]',$_POST['name'],time()+$_POST['time']);
        setcookie('xzt[pw]',md5($_POST['pw']),time()+$_POST['time']);
        skip('index.php','ok','登录成功！');
    }else{
        skip('login.php','error','用户名或密码填写错误！');
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
	<div style="margin-top:95px;"></div>
	<div id="register" class="auto">
		<h2>欢迎登录成为XZT会员</h2>
		<form method="post">
			<label>用户名：<input type="text" name="name"  /><span></span></label>
			<label>密码：<input type="password" name="pw"  /><span></span></label>
			<label>验证码：<input type="text" name="vcode"  /><span>*请输入下方验证码</span></label>
			<img class="vcode" src="show_vcode.php" />
			<label>自动登录：
				<select style="width:236px;height:25px;" name="time">
					<option value="3600">1小时内</option>
					<option value="86400">1天内</option>
					<option value="259200">3天内</option>
					<option value="2592000">30天内</option>
				</select>
				<span>*公共电脑上请勿长期自动登录</span>
			</label>
			<div style="clear:both;"></div>
			<input class="btn" type="submit"name="submit" value="登录" />
		</form>
	</div>
	<div id="footer" class="auto">
		<div class="bottom">
			<a>XZT</a>
		</div>
		<div class="copyright">Powered by XZT ©2015 XZT.com</div>
	</div>
</body>
</html>