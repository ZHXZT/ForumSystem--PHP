<?php
include_once '../inc/config.inc.php';
include_once '../inc/mysql.inc.php';
include_once '../inc/tool.inc.php';
$link = connect();
//验证后台是否登录
if (!is_manage_login($link)){
    header('Location:login.php');
    exit();
}
if (basename($_SERVER['SCRIPT_NAME'])=='manage_add.php' || basename($_SERVER['SCRIPT_NAME'])=='manage.php'){
    if ($_SESSION['manage']['level']!='0'){
        if (!isset($_SERVER['HTTP_REFERER'])){
            $_SERVER['HTTP_REFERER']='index.php';
        }
        skip($_SERVER['HTTP_REFERER'], 'error', '你的权限不足！');
    }
}

if (isset($_POST['submit'])) {

    if (empty($_POST['name'])) {
        skip('manage_add.php', 'error', '名称不得为空！');
    }
    if (mb_strlen($_POST['name']) > 32) {
        skip('manage_add.php', 'error', '名称不得多余32字！');
    }
    if (mb_strlen($_POST['pw']) < 6) {
        skip('manage_add.php', 'error', '密码不得少于6位！');
    }
    $_POST=escape($link,$_POST);
    $query="select * from xzt_manage where name='{$_POST['name']}'";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)){
        skip('manage_add.php', 'error', '该名称已经存在！');
    }
    if (!isset($_POST['level'])){
        $_POST['level']=1;
    }elseif ($_POST['level']=='0'){
        $_POST['level']=0;
    }elseif ($_POST['level']=='1'){
        $_POST['level']=1;
    }else{
        $_POST['level']=1;
    }


    $query="insert into xzt_manage(name,pw,create_time,level ) values ('{$_POST["name"]}',md5({$_POST['pw']}),now(),{$_POST['level']})";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){
        skip('manage_add.php', 'ok', '添加成功！');
    }else{
        skip('manage_add.php', 'error', '添加失败！');
    }


}


?>
<?php include "inc/header.inc.php";?>
<div id="main">
    <div class="title" style="margin-bottom: 20px">添加管理员</div>
    <form method="post">
        <table class="au">
            <tr>
                <td>管理员名称</td>
                <td><input type="text" name="name"></td>
                <td>名称不得为空，不得多于32字</td>
            </tr>
            <tr>
                <td>密码</td>
                <td><input type="text" name="pw"></td>
                <td>不得少于6位</td>
            </tr>
            <tr>
                <td>等级</td>
                <td>
                    <select name="level">
                        <option value="1">普通管理员</option>
                        <option value="0">超级管理员</option>
                    </select>
                </td>
                <td>请选择一个等级</td>
            </tr>
        </table>
        <input class="btn" type="submit" name="submit" value="添加" style="margin-top: 20px;cursor: pointer"/>
    </form>
</div>

<?php include "inc/footer.inc.php";?>

