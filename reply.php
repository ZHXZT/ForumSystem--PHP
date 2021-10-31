<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
$link=connect();
if (!$member_id=is_login($link)){
    skip('login.php','error','请先登录！');
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
    skip('index.php','error','id参数不合法！');
}

$query="select sc.id,sc.title,sm.name from xzt_content sc,xzt_member sm where sc.id={$_GET['id']} and sc.member_id=sm.id";
$result_content=execute($link,$query);
if (mysqli_num_rows($result_content)!=1){
    skip('index.php','error','你要回复的帖子不存在！');
}

if (isset($_POST['submit'])){
    if (mb_strlen($_POST['content'])<3){
        skip($_SERVER['REQUEST_URI'],'error','回复内容不得少于三字！');
    }
    $_POST=escape($link,$_POST);
    $query="insert into xzt_reply(content_id,content,time,member_id) values ({$_GET['id']},'{$_POST['content']}',now(),{$member_id})";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){
        skip("show.php?id={$_GET['id']}",'ok','回复成功！');
    }else{
        skip($_SERVER['REQUEST_URI'],'error','回复失败！请重试');
    }
}

$data_content=mysqli_fetch_assoc($result_content);
$data_content['title']=htmlspecialchars($data_content['title']);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" type="text/css" href="style/public.css" />
    <link rel="stylesheet" type="text/css" href="style/publish.css" />
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
            <?php
            if ($member_id){
                $str=<<<A
                <a>你好，{$_COOKIE['xzt']['name']}</a>
A;
                echo $str;
            }else{
                $str=<<<A
                <a href="login.php">登录</a>&nbsp
                <a href="register.php">注册</a>
A;
                echo $str;
            }
            ?>
        </div>
    </div>
</div>
<div style="margin-top:55px;"></div>
<div id="position" class="auto">
    <a href="index.php">首页</a> &gt; 回复帖子
</div>
<div id="publish">
    <div>回复：由 <?php echo $data_content['name']?> 发布的: <?php echo $data_content['title']?></div>
    <form method="post">
        <textarea name="content" class="content"></textarea>
        <input class="reply" type="submit" name="submit" value="" />
        <div style="clear:both;"></div>
    </form>
</div>
<div id="footer" class="auto">
    <div class="bottom">
        <a>私房库</a>
    </div>
    <div class="copyright">Powered by sifangku ©2015 sifangku.com</div>
</div>
</body>
</html>


