<?php
if (file_exists('inc/install.lock')){
    header("Location:index.php");
}
header('Content-type:text/html;charset=utf-8');
//检测php版本
if (version_compare(PHP_VERSION,'5.4.0')<0){
    exit('你的php版本为'.PHP_VERSION.'！程序要求php版本不得低于5.4.0');
}

if (isset($_POST['submit'])){
    if (empty($_POST['db_host'])){
        exit('数据库地址不得为空！<a href="install.php">点击返回</a>');
    }
    if (empty($_POST['db_port'])){
        exit('数据库服务端口不得为空！<a href="install.php">点击返回</a>');
    }
    if (empty($_POST['db_user'])){
        exit('数据库用户名不得为空！<a href="install.php">点击返回</a>');
    }
    if (!isset($_POST['db_pw'])){
        exit('数据库密码不存在！<a href="install.php">点击返回</a>');
    }
    if (empty($_POST['db_database'])){
        exit('数据库名称不得为空！<a href="install.php">点击返回</a>');
    }
    $_POST['manage_name']='admin';
    if (empty($_POST['manage_pw']) || mb_strlen($_POST['manage_pw'])<6){
        exit('后台管理员密码不得少于6位！<a href="install.php">点击返回</a>');
    }
    if (empty($_POST['manage_pw_confirm']) || $_POST['manage_pw']!=$_POST['manage_pw_confirm']){
        exit('两次密码不一致！<a href="install.php">点击返回</a>');
    }
    $link=@mysqli_connect($_POST['db_host'],$_POST['db_user'],$_POST['db_pw'],'',$_POST['port']);
    if(mysqli_connect_errno()){
        exit('数据库连接失败，请填写正确的数据库连接信息！<a href="install.php">点击返回</a>');
    }
    if (!mysqli_select_db($link,$_POST['db_database'])){
        $query="CREATE DATABASE IF NOT EXISTS`{$_POST['db_database']}`DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        mysqli_query($link,$query);
        if (mysqli_errno($link)){
            exit('数据库创建失败，请检查账户权限！<a href="install.php">点击返回</a>');
        }
        mysqli_select_db($link,$_POST['db_database']);
    }

    $query=array();
    $query['xzt_content']="
		CREATE TABLE IF NOT EXISTS `xzt_content` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `module_id` int(10) unsigned NOT NULL,
		  `title` varchar(255) NOT NULL,
		  `content` text NOT NULL,
		  `time` datetime NOT NULL,
		  `member_id` int(10) unsigned NOT NULL,
		  `times` int(10) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	";
    $query['xzt_father_module']="
		CREATE TABLE IF NOT EXISTS `xzt_father_module` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `module_name` varchar(66) NOT NULL,
		  `sort` int(11) DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='父版块信息表' AUTO_INCREMENT=1;	
	";
    $query['xzt_info']="
		CREATE TABLE IF NOT EXISTS `xzt_info` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `title` varchar(255) NOT NULL,
		  `keywords` varchar(255) NOT NULL,
		  `description` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	";
    $query['xzt_manage']="
		CREATE TABLE IF NOT EXISTS `xzt_manage` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(32) NOT NULL,
		  `pw` varchar(32) NOT NULL,
		  `create_time` datetime NOT NULL,
		  `level` tinyint(4) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;		
	";
    $query['xzt_member']="
		CREATE TABLE IF NOT EXISTS `xzt_member` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(32) NOT NULL,
		  `pw` varchar(32) NOT NULL,
		  `photo` varchar(255) NOT NULL,
		  `register_time` datetime NOT NULL,
		  `last_time` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8;
	";
    $query['xzt_reply']="
		CREATE TABLE IF NOT EXISTS `xzt_reply` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `content_id` int(10) unsigned NOT NULL,
		  `quote_id` int(10) unsigned NOT NULL DEFAULT '0',
		  `content` text NOT NULL,
		  `time` datetime NOT NULL,
		  `member_id` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	";
    $query['xzt_son_module']="
		CREATE TABLE IF NOT EXISTS `xzt_son_module` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `father_module_id` int(10) unsigned NOT NULL,
		  `module_name` varchar(66) NOT NULL,
		  `info` varchar(255) NOT NULL,
		  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
		  `sort` int(10) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
	";
    foreach ($query as $key=>$val){
        mysqli_query($link,$val);
        if(mysqli_errno($link)){
            echo "数据表 {$key} 创建失败，请检查数据库账户是否有创建表的权限！<a href='install.php'>点击返回</a>";
            exit();
        }
    }
    $query_info_s="select * from xzt_info where id=1";
    $result=mysqli_query($link, $query_info_s);
    if(mysqli_num_rows($result)!=1){
        $query_info_i="INSERT INTO `xzt_info` (`id`, `title`, `keywords`, `description`) VALUES(1, 'xztbbs', '私房库', '私房库');";
        mysqli_query($link,$query_info_i);
        if(mysqli_errno($link)){
            exit("数据库xzt_info写入数据失败请检查相应权限!<a href='install.php'>点击返回</a>");
        }
    }
    $query_manage_s="select * from xzt_manage where name='admin'";
    $result=mysqli_query($link, $query_manage_s);
    if(mysqli_num_rows($result)!=1){
        $query_manage_i="INSERT INTO `xzt_manage` (`name`, `pw`, `create_time`, `level`) VALUES('admin',md5('{$_POST['manage_pw']}'),now(), 0)";
        mysqli_query($link,$query_manage_i);
        if(mysqli_errno($link)){
            exit("管理员创建失败，请检查数据表xzt_manage是否具有写权限!<a href='install.php'>点击返回</a>");
        }
    }

    $filename='inc/config.inc.php';
    $str_file=file_get_contents($filename);
    $pattern="/'DB_HOST',.*?\)/";
    if (preg_match($pattern,$str_file)){
        $_POST['db_host']=addslashes($_POST['db_host']);
        $str_file=preg_replace($pattern,"'DB_HOST','{$_POST['db_host']}')",$str_file);
    }
    $pattern="/'DB_USER',.*?\)/";
    if (preg_match($pattern,$str_file)){
        $_POST['db_user']=addslashes($_POST['db_user']);
        $str_file=preg_replace($pattern,"'DB_USER','{$_POST['db_user']}')",$str_file);
    }
    $pattern="/'DB_PASSWORD',.*?\)/";
    if (preg_match($pattern,$str_file)){
        $_POST['db_pw']=addslashes($_POST['db_pw']);
        $str_file=preg_replace($pattern,"'DB_PASSWORD','{$_POST['db_pw']}')",$str_file);
    }
    $pattern="/'DB_DATABASE',.*?\)/";
    if (preg_match($pattern,$str_file)){
        $_POST['db_database']=addslashes($_POST['db_database']);
        $str_file=preg_replace($pattern,"'DB_DATABASE','{$_POST['db_database']}')",$str_file);
    }
    $pattern="/\('DB_PORT',.*?\)/";
    if (preg_match($pattern,$str_file)){
        $_POST['db_port']=addslashes($_POST['db_port']);
        $str_file=preg_replace($pattern,"('DB_PORT','{$_POST['db_port']}')",$str_file);
    }if (!file_put_contents($filename,$str_file)){
        exit("配置文件写入失败，请检查是否具有写权限!<a href='install.php'>点击返回</a>");
    }
    if (!file_put_contents('inc/install.lock','系统文件')){
        exit("系统安装成功，但inc/install.lock创建失败！");
    }
    echo "恭喜安装成功！<a href='index.php'>访问首页</a> | <a href='admin/login.php'>访问后台</a>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title>欢迎使用 本引导安装程序</title>
    <meta name="keywords" content="欢迎使用本引导安装程序" />
    <meta name="description" content="欢迎使用本引导安装程序" />
    <style type="text/css">
        body {
            background:#f7f7f7;
            font-size:14px;
        }
        #main {
            width:560px;
            height:490px;
            background:#fff;
            border:1px solid #ddd;
            position:absolute;
            top:50%;
            left:50%;
            margin-left:-280px;
            margin-top:-280px;
        }
        #main .title {
            height: 48px;
            line-height: 48px;
            color:#333;
            font-size:16px;
            font-weight:bold;
            text-indent:30px;
            border-bottom:1px dashed #eee;
        }
        #main form {
            width:400px;
            margin:20px 0 0 10px;
        }
        #main form label {
            margin:10px 0 0 0;
            display:block;
            text-align:right;
        }
        #main form label input.text {
            width:200px;
            height:25px;
        }

        #main form label input.submit {
            width:204px;
            display:block;
            height:35px;
            cursor:pointer;
            float:right;
        }
    </style>
</head>
<body>
<div id="main">
    <div class="title">欢迎使用 本引导安装程序</div>
    <form method="post">
        <label>数据库地址：<input class="text" type="text" name="db_host" value="localhost" /></label>
        <label>端口：<input class="text" type="text" name="db_port" value="3306" /></label>
        <label>数据库用户名：<input class="text" type="text" name="db_user" /></label>
        <label>数据库密码：<input class="text" type="text" name="db_pw" /></label>
        <label>数据库名称：<input class="text" type="text" name="db_database" /></label>
        <br /><br />
        <label>后台管理员名称：<input class="text" type="text" name="manage_name" readonly="readonly" value="admin" /></label>
        <label>密码：<input class="text" type="password" name="manage_pw" /></label>
        <label>密码确认：<input class="text" type="password" name="manage_pw_confirm" /></label>
        <label><input class="submit" type="submit" name="submit" value="确定安装" /></label>
    </form>
</div>
</body>
</html>
