<?php
include_once 'inc/config.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/page.inc.php';
$link=connect();
$member_id=is_login($link);
$is_manage_login=is_manage_login($link);
if (!isset($_GET['id'])||!is_numeric($_GET['id'])){
    skip('index.php','error','id参数不合法！');
}
$query="select * from xzt_son_module where id={$_GET['id']}";
$result_son=execute($link,$query);
if (mysqli_num_rows($result_son)!=1){
    skip('index.php','error','子板块不存在！');
}
$data_son=mysqli_fetch_assoc($result_son);
$query="select * from xzt_father_module where id={$data_son['father_module_id']}";
$result_father=execute($link,$query);
$data_father=mysqli_fetch_assoc($result_father);

$query="select count(*) from xzt_content where module_id={$_GET['id']}";
$count_all=num($link,$query);
$query="select count(*) from xzt_content where module_id={$_GET['id']} and time >CURDATE()";
$count_today=num($link,$query);
$query="select * from xzt_member where id={$data_son['member_id']}";
$result_member=execute($link,$query);



?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" type="text/css" href="style/public.css" />
    <link rel="stylesheet" type="text/css" href="style/list.css" />
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

    <a href="index.php">首页</a> &gt; <a href="list_father.php?id=<?php echo $data_father['id']?>"><?php echo $data_father['module_name']?></a> &gt;<?php echo $data_son['module_name']?>
</div>
<div id="main" class="auto">
    <div id="left">
        <div class="box_wrap">
            <h3><?php echo $data_son['module_name']?></h3>
            <div class="num">
                今日：<span><?php echo $count_today?></span>&nbsp;&nbsp;&nbsp;
                总帖：<span><span><?php echo $count_all?></span>
            </div>
            <div class="moderator">版主：<span><?php
                    if (mysqli_num_rows($result_member)==0){
                        echo "暂无版主";
                    }else{
                        $data_member=mysqli_fetch_assoc($result_member);
                        echo $data_member['name'];
                    }
                    ?></span></div>
            <div class="notice"><?php echo $data_son['info']?></div>
            <div class="pages_wrap">
                <a class="btn publish" href="publish.php?son_module_id=<?php echo $_GET['id']?>" target="_blank"></a>
                <div class="pages">
                    <?php
                    $page=page($count_all,10);
                    echo $page['html'];
                    ?>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <ul class="postsList">
            <?php
            $query="select xzt_content.title,xzt_content.id,xzt_content.time,xzt_content.times,xzt_content.member_id,xzt_member.name,xzt_member.photo from xzt_content,xzt_member where xzt_content.module_id={$_GET['id']} and xzt_content.member_id=xzt_member.id {$page['limit']}";
            $result_content=execute($link,$query);
            while ($data_content=mysqli_fetch_assoc($result_content)){
                $data_content['title']=htmlspecialchars($data_content['title']);

                $query="select time from xzt_reply where content_id={$data_content['id']} order by id desc limit 1";
                $result_last_reply=execute($link,$query);
                if (mysqli_num_rows($result_last_reply)==0){
                    $last_time='暂无';
                }else{
                    $data_last_reply=mysqli_fetch_assoc($result_last_reply);
                    $last_time=$data_last_reply['time'];
                }
                $query="select count(*) from xzt_reply where content_id={$data_content['id']}";

            ?>
                <li>
                    <div class="smallPic">
                        <a target="_blank" href="member.php?id=<?php echo $data_content['member_id']?>">
                            <img width="45" height="45"src="<?php if ($data_content['photo']!=''){echo $data_content['photo'];}else{echo 'style/photo.jpg';}?>">
                        </a>
                    </div>
                    <div class="subject">
                        <div class="titleWrap"><h2><a href="show.php?id=<?php echo $data_content['id']?>"><?php echo $data_content['title'] ?></a></h2></div>
                        <p>
                            楼主:<?php echo $data_content['name'] ?>&nbsp;&nbsp;<?php echo $data_content['time'] ?>&nbsp;&nbsp;&nbsp;&nbsp;最后回复：<?php echo $last_time?><br>
                            <?php
                            if (check_user($member_id,$data_content['member_id'],$is_manage_login)){
                                $return_url=urlencode($_SERVER['REQUEST_URI']);
                                $url=urlencode("content_delete.php?id={$data_content['id']}&return_url={$return_url}");
                                $delete_url="confirm.php?url={$url}&return_url={$return_url}";
                                echo "<a href='content_update.php?id={$data_content['id']}&return_url={$return_url}'>编辑</a> <a href='{$delete_url}'>删除</a>";
                            }
                            ?>
                        </p>
                    </div>
                    <div class="count">
                        <p>
                            回复<br /><span><?php echo num($link,$query)?></span>
                        </p>
                        <p>
                            浏览<br /><span><?php echo $data_content['times'] ?></span>
                        </p>
                    </div>
                    <div style="clear:both;"></div>
                </li>
                <?php
            }
            ?>
        </ul>
        <div class="pages_wrap">
            <a class="btn publish" href="publish.php?son_module_id=<?php echo $_GET['id']?>" target="_blank"></a>
            <div class="pages">
                <?php
                $page=page($count_all,1);
                echo $page['html'];
                ?>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div id="right">
        <div class="classList">
            <div class="title">版块列表</div>
            <ul class="listWrap">
                <?php
                $query="select * from xzt_father_module";
                $result_father=execute($link,$query);
                while ($data_father=mysqli_fetch_assoc($result_father)){
                    ?>
                    <li>
                        <h2><a href="list_father.php?id=<?php echo $data_father['id']?>"><?php echo $data_father['module_name']?></a></h2>
                        <ul>
                            <?php
                            $query="select * from xzt_son_module where father_module_id={$data_father['id']}";
                            $result_son=execute($link,$query);
                            while ($data_son=mysqli_fetch_assoc($result_son)){
                                ?>
                                <li><h3><a href="list_son.php?id=<?php echo $data_son['id']?>"><?php echo $data_son['module_name']?></a></h3></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>
<div id="footer" class="auto">
    <div class="bottom">
        <a>XZT</a>
    </div>
    <div class="copyright">Powered by XZT ©2015 XZT.com</div>
</div>
</body>
</html>

