<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/page.inc.php';
$link=connect();
$member_id=is_login($link);

if (!isset($_GET['id'])||!is_numeric($_GET['id'])){
    skip('index.php','error','帖子id不合法！');
}
$query="select sc.id cid,sc.module_id,sc.title,sc.content,sc.time,sc.member_id,sc.times,sm.name,sm.photo from xzt_content sc,xzt_member sm where sc.id={$_GET['id']} and sc.member_id=sm.id";
$result_content=execute($link,$query);
if (mysqli_num_rows($result_content)!=1){
    skip('index.php','error','帖子不存在！');
}
$query="update xzt_content set times=times+1 where id={$_GET['id']}";
execute($link,$query);
$data_content=mysqli_fetch_assoc($result_content);
$data_content['times']=$data_content['times']+1;
$data_content['title']=htmlspecialchars($data_content['title']);
$data_content['content']=nl2br(htmlspecialchars($data_content['content']));

$query="select * from xzt_son_module where id={$data_content['module_id']}";
$result_son=execute($link,$query);
$data_son=mysqli_fetch_assoc($result_son);

$query="select * from xzt_father_module where id={$data_son['father_module_id']}";
$result_father=execute($link,$query);
$data_father=mysqli_fetch_assoc($result_father);

$query="select count(*) from xzt_reply where content_id={$_GET['id']}";
$count_reply=num($link,$query);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" type="text/css" href="style/public.css" />
    <link rel="stylesheet" type="text/css" href="style/show.css" />
</head>
<body>
<div class="header_wrap">
    <div id="header" class="auto">
        <div class="logo">XZT</div>
        <div class="nav">
            <a class="hover" href="index.php">首页</a>
        </div>
        <div class="serarch">
            <form action="search.php" method="get">
                <input class="keyword" type="text" name="keyword"  placeholder="搜索其实很简单" />
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
    <a href="index.php">首页</a> &gt;
    <a href="list_father.php?id=<?php echo $data_father['id']?>"><?php echo $data_father['module_name']?></a>&gt;
    <a href="list_son.php?id=<?php echo $data_son['id']?>"><?php echo $data_son['module_name']?></a> &gt;
    <?php echo $data_content['title']?>
</div>
<div id="main" class="auto">
    <div class="wrap1">
        <div class="pages">
            <?php
            $query="select count(*) from xzt_reply where content_id={$_GET['id']}";
            $count_reply=num($link,$query);
            $page_size=10;
            $page=page($count_reply,$page_size);
            echo $page['html'];
            ?>
        </div>
        <a class="btn reply" href="reply.php?id=<?php echo $_GET['id']?>"></a>
        <div style="clear:both;"></div>
    </div>
    <?php
    if ($_GET['page']==1){

    ?>
    <div class="wrapContent">
        <div class="left">
            <div class="face">
                    <img width=120 height=120 src="<?php if ($data_content['photo']!=''){echo $data_content['photo'];}else{echo 'style/photo.jpg';}?>" />
            </div>
            <div class="name">
                <a href=""><?php echo $data_content['name']?></a>
            </div>
        </div>
        <div class="right">
            <div class="title">
                <h2><?php echo $data_content['title']?></h2>
                <span>阅读：<?php echo $data_content['times']?>&nbsp;|&nbsp;回复：<?php echo $count_reply?></span>
                <div style="clear:both;"></div>
            </div>
            <div class="pubdate">
                <span class="date">发布于：<?php echo $data_content['time']?> </span>
                <span class="floor" style="color:red;font-size:14px;font-weight:bold;">楼主</span>
            </div>
            <div class="content">
                <?php echo $data_content['content']?>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <?php
    }
    ?>


    <?php
    $query="select sm.name,sr.member_id,sr.quote_id,sm.photo,sr.time,sr.id,sr.content from xzt_reply sr,xzt_member sm where sr.member_id=sm.id and sr.content_id={$_GET['id']} order by id asc {$page['limit']}";
    $result_reply=execute($link,$query);
    $i=($_GET['page']-1)*$page_size+1;
    while ($data_reply=mysqli_fetch_assoc($result_reply)){
        ?>
    <div class="wrapContent">
        <div class="left">
            <div class="face">
                <a target="_blank" data-uid="2374101" href="">
                    <img width=120 height=120 src="<?php if ($data_reply['photo']!=''){echo $data_reply['photo'];}else{echo 'style/photo.jpg';}?>" />
                </a>
            </div>
            <div class="name">
                <a href=""><?php echo $data_reply['name']?></a>
            </div>
        </div>
        <div class="right">

            <div class="pubdate">
                <span class="date">回复时间：<?php echo $data_reply['time']?></span>
                <span class="floor"><?php echo $i++ ?>楼&nbsp;|&nbsp;<a href="quote.php?id=<?php echo $_GET['id']?>&reply_id=<?php echo $data_reply['id']?>">引用</a></span>
            </div>
            <div class="content">
                <?php
                if ($data_reply['quote_id']){
                    $query="select count(*) from xzt_reply where content_id={$_GET['id']} and id<={$data_reply['quote_id']}";
                    $floor=num($link,$query);

                    $query="select xzt_reply.content,xzt_member.name from xzt_reply,xzt_member where xzt_reply.id={$data_reply['quote_id']} and xzt_reply.content_id={$_GET['id']} and xzt_reply.member_id=xzt_member.id";
                    $result_quote=execute($link,$query);
                    $data_quote=mysqli_fetch_assoc($result_quote);
                ?>
                <div class="quote">
                    <h2>引用<?php echo $floor?>楼<?php echo $data_quote['name']?> 发表的：</h2>
                    <?php echo nl2br(htmlspecialchars($data_quote['content']))?>
                </div>
                <?php
                }
                ?>

                <?php
                $data_reply['content']=htmlspecialchars($data_reply['content']);
                echo $data_reply['content'];
                ?>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <?php
    }
    ?>
    <div class="wrap1">
        <div class="pages">
            <?php
            echo $page['html'];
            ?>
        </div>
        <a class="btn reply" href="reply.php?id=<?php echo $_GET['id']?>"></a>
        <div style="clear:both;"></div>
    </div>
</div>
<div id="footer" class="auto">
    <div class="bottom">
        <a>XZT</a>
    </div>
    <div class="copyright">Powered by XZT ©2015 XZT.com</div>
</div>
</body>
</html>
