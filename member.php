<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/page.inc.php';
$link=connect();
$member_id=is_login($link);
$is_manage_login=is_manage_login($link);
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
	skip('index.php', 'error', '会员id参数不合法!');
}
$query="select * from xzt_member where id={$_GET['id']}";
$result_member=execute($link, $query);
if(mysqli_num_rows($result_member)!=1){
	skip('index.php', 'error', '你所访问的会员不存在!');
}
$data_member=mysqli_fetch_assoc($result_member);
$query="select count(*) from xzt_content where member_id={$_GET['id']}";
$count_all=num($link, $query);

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
        <style type="text/css">
            #main #right .member_big {
                margin:20px auto 0 auto;
                width:180px;
            }
            #main #right .member_big dl dd {
                line-height:150%;
            }
            #main #right .member_big dl dd a {
                color:#333;
            }
            #main #right .member_big dl dd.name {
                font-size: 22px;
                font-weight: 400;
                line-height:140%;
                padding:5px 0 10px 0px;
            }
        </style>
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
                <a>你好，{$_COOKIE['xzt']['name']}</a><span style="color: aliceblue"> | </span><a href="logout.php">退出</a>
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
	<a href="index.php">首页</a> &gt; <?php echo $data_member['name']?>
</div>
<div id="main" class="auto">
	<div id="left">
		<ul class="postsList">
			<?php 
			$page=page($count_all,5);
			$query="select xzt_content.title,xzt_content.id,xzt_content.time,xzt_content.member_id,xzt_content.times,xzt_member.name,xzt_member.photo from xzt_content,xzt_member where xzt_content.member_id={$_GET['id']} and xzt_content.member_id=xzt_member.id order by id desc {$page['limit']}";
			$result_content=execute($link, $query);
			while($data_content=mysqli_fetch_assoc($result_content)){
				$data_content['title']=htmlspecialchars($data_content['title']);
				$query="select time from xzt_reply where content_id={$data_content['id']} order by id desc limit 1";
				$result_last_reply=execute($link, $query);
				if(mysqli_num_rows($result_last_reply)==0){
					$last_time='暂无';
				}else{
					$data_last_reply=mysqli_fetch_assoc($result_last_reply);
					$last_time=$data_last_reply['time'];
				}
				$query="select count(*) from xzt_reply where content_id={$data_content['id']}";
			?>
			<li>
				<div class="smallPic">
                    <img width="45" height="45" src="<?php if($data_content['photo']!=''){echo $data_content['photo'];}else{echo 'style/photo.jpg';}?>" />
				</div>
				<div class="subject">
					<div class="titleWrap"><h2><a target="_blank" href="show.php?id=<?php echo $data_content['id']?>"><?php echo $data_content['title']?></a></h2></div>
					<p>
                        <?php
                        if (check_user($member_id,$data_content['member_id'],$is_manage_login)){
                            $url=urlencode("content_delete.php?id={$data_content['id']}");
                            $return_url=urlencode($_SERVER['REQUEST_URI']);
                            $delete_url="confirm.php?url={$url}&return_url={$return_url}";
                            echo "<a href='content_update.php?id={$data_content['id']}'>编辑</a> <a href='{$delete_url}'>删除</a>";
                        }
                        ?>
						发帖日期：<?php echo $data_content['time']?>&nbsp;&nbsp;&nbsp;&nbsp;最后回复：<?php echo $last_time?>
					</p>
				</div>
				<div class="count">
					<p>
						回复<br /><span><?php echo num($link,$query)?></span>
					</p>
					<p>
						浏览<br /><span><?php echo $data_content['times']?></span>
					</p>
				</div>
				<div style="clear:both;"></div>
			</li>
			<?php }?>
		</ul>
		<div class="pages">
			<?php 
			echo $page['html'];
			?>
		</div>
	</div>
	<div id="right">
		<div class="member_big">
			<dl>
				<dt>
					<img width="180" height="180" src="<?php if($data_member['photo']!=''){echo $data_member['photo'];}else{echo 'style/photo.jpg';}?>" />
				</dt>
				<dd class="name"><?php echo $data_member['name']?></dd>
				<dd>帖子总计：<?php echo $count_all?></dd>
                <?php
                if ($member_id==$data_member['id']){
                ?>
				<dd>操作：<a target="_blank" href="member_photo_update.php">修改头像</a> | <a target="_blank" href="">修改密码</a></dd>
                <?php
                }
                ?>
			</dl>
			<div style="clear:both;"></div>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
