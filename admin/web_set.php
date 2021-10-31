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
$query="select * from xzt_info where id=1";
$result_info=execute($link,$query);
$data_info=mysqli_fetch_assoc($result_info);

if (isset($_POST['submit'])){
    $_POST=escape($link,$_POST);
    $query="update xzt_info set title='{$_POST['title']}',keywords='{$_POST['keywords']}',description='{$_POST['description']}' where id=1";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){
        skip('web_set.php','ok','修改成功！');
    }else{
        skip('web_set.php','error','修改失败，请重试！');
    }
}
?>

<?php include "inc/header.inc.php";?>

<div id="main">
    <div class="title" style="margin-bottom: 20px">网站设置</div>
    <form method="post">
        <table class="au">
            <tr>
                <td>网站标题</td>
                <td><input type="text" name="title" value="<?php echo $data_info['title']?>"></td>
                <td>前台页面的标题</td>
            </tr>
            <tr>
                <td>关键字</td>
                <td>
                <input name="keywords" type="text" value="<?php echo $data_info['keywords']?>">
                </td>
                <td>关键字</td>
            </tr>
            <tr>
                <td>描述</td>
                <td>
                    <textarea name="description" ><?php echo $data_info['description']?></textarea>
                </td>
                <td>描述</td>
            </tr>
        </table>
        <input class="btn" type="submit" name="submit" value="设置" style="margin-top: 20px;cursor: pointer"/>
    </form>
</div>

<?php include "inc/footer.inc.php";?>
