<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
$link=connect();
if (!$member_id=is_login($link)){
    skip('login.php','error','请先登录！');
}
if (isset($_POST['submit'])){
    if (empty($_POST['module_id'])||!is_numeric($_POST['module_id'])){
        skip('publish.php','error','所属板块id不合法！');
    }
    $query="select * from xzt_son_module where id={$_POST['module_id']}";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)!=1){
        skip('publish.php','error','所属板块不存在!');
    }
    if (empty($_POST['title'])){
        skip('publish.php','error','标题不得为空！');
    }
    if (mb_strlen($_POST['title'])>255){
        skip('publish.php','error','标题不得长于255字符');
    }
    $_POST=escape($link,$_POST);
    //$query="insert into xzt_content(module_id,title,content,time,member_id) values ({$_POST['module_id']},'{$_POST['title']}','{$_POST['content']}',now(),{$_POST['member_id']})";
    $query="insert into xzt_content(module_id,title,content,time,member_id) values({$_POST['module_id']},'{$_POST['title']}','{$_POST['content']}',now(),{$member_id})";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){
        skip('publish.php','ok','发布成功！');
    }else{
        skip('publish.php','error','发布失败，请重试！');
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
                <a>登录</a>&nbsp
                <a>注册</a>
A;

            }
            ?>
        </div>
    </div>
</div>
<div style="margin-top:55px;"></div>
<div id="position" class="auto">
    <a href="index.php">首页</a> &gt; 发布帖子
</div>
<div id="publish">
    <form method="post">
        <select name="module_id">
            <option>请选择一个版块</option>
            <?php
            $where='';
            if (isset($_GET['father_module_id']) && is_numeric($_GET['father_module_id'])){
                $where="where id={$_GET['father_module_id']} ";
            }
            $query="select * from xzt_father_module {$where}order by sort desc ";
            $result_father=execute($link,$query);
            while ($data_father=mysqli_fetch_assoc($result_father)){
                echo "<optgroup label='{$data_father['module_name']}'>";
                $query="select * from xzt_son_module where father_module_id={$data_father['id']} order by sort desc ";
                $result_son=execute($link,$query);
                while ($data_son=mysqli_fetch_assoc($result_son)){
                    if (isset($_GET['son_module_id']) && $_GET['son_module_id']==$data_son['id']){
                        echo "<option selected='selected' value='{$data_son['id']}'>{$data_son['module_name']}</option>";
                    }else{
                        echo "<option value='{$data_son['id']}'>{$data_son['module_name']}</option>";
                    }
                }
                echo "</optgroup>";
            }
            ?>
        </select>
        <input class="title" placeholder="请输入标题" name="title" type="text" />
        <textarea name="content" class="content"></textarea>
        <input class="publish" type="submit" name="submit" value="" />
        <div style="clear:both;"></div>
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
