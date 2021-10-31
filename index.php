<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
$link=connect();
$member_id=is_login($link);

$query="select * from xzt_info where id=1";
$result_info=execute($link,$query);
$data_info=mysqli_fetch_assoc($result_info);
?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title><?php echo $data_info['title']?></title>
    <meta name="keywords" content="<?php echo $data_info['keywords']?>" />
    <meta name="description" content="<?php echo $data_info['description']?>" />
    <link rel="stylesheet" type="text/css" href="style/public.css" />
    <link rel="stylesheet" type="text/css" href="style/index.css" />
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
                <a href="member.php?id={$member_id}" target="_blank">你好，{$_COOKIE['xzt']['name']}</a><span style="color: aliceblue"> | </span><a href="logout.php">退出</a>
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
<div style="margin-top:80px;"></div>
<div id="hot" class="auto">
    <div class="title">热门动态</div>
    <ul class="newlist">
        <!-- 20条 -->
        <li><a href="#">[php]</a> <a href="#">实战项目...</a></li>

    </ul>
    <div style="clear:both;"></div>
</div>
<?php
$query="select * from xzt_father_module order by sort desc ";
$result_father=execute($link,$query);
while ($data_father=mysqli_fetch_assoc($result_father)){?>
    <div class="box auto">
        <div class="title">
            <a href="list_father.php?id=<?php echo $data_father['id']?>"><?php echo $data_father['module_name']?></a>
        </div>
        <div class="classList">
            <?php
            $query="select * from xzt_son_module where father_module_id={$data_father['id']}";
            $result_son=execute($link,$query);
            if (mysqli_num_rows($result_son)){
                while ($data_son=mysqli_fetch_assoc($result_son)){
                    $query="select count(*) from xzt_content where module_id={$data_son['id']} and time >CURDATE()";
                    $count_today=num($link,$query);
                    $query="select count(*) from xzt_content where module_id={$data_son['id']}";
                    $count_all=num($link,$query);
                    $html=<<<A
<div class="childBox new">
<h2><a href="list_son.php?id={$data_son['id']}">{$data_son['module_name']} </a><span>(今日$count_today)</span></h2>
帖子：$count_all<br>
</div>
A;
                    echo $html;
                }
            }else{
                echo '<div style="padding: 10px 0">暂无子板块...</div>';
            }
            ?>
            <div style="clear: both"></div>
        </div>

    </div>


<?php }?>

