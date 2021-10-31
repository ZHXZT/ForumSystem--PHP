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

if (isset($_POST['submit'])){

    if (empty($_POST['module_name'])){
        skip('father_module_add.php','error','板块名不得为空');
    }
    if (mb_strlen($_POST['module_name'])>66){
        skip('father_module_add.php','error','板块名不得多余66字符');
    }
    if (!is_numeric($_POST['sort'])){
        skip('father_module_add.php','error','排序只能为数字');
    }
    $_POST=escape($link,$_POST);
    $query="select * from xzt_father_module where module_name='{$_POST['module_name']}'";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)){
        skip('father_module_add.php','error','这个板块已经存在');
    }

    $query="insert into xzt_father_module(module_name,sort) values ('{$_POST['module_name']}',{$_POST['sort']})";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){
        skip('father_module.php','ok','success');
    }else{
        skip('father_module_add.php','error','fail');
    }
}

?>
<?php include "inc/header.inc.php";?>
<div id="main">
    <div class="title" style="margin-bottom: 20px">添加父板块</div>
    <form method="post">
        <table class="au">
            <tr>
                <td>板块名称</td>
                <td><input type="text" name="module_name"></td>
                <td>板块名称不得为空</td>
            </tr>
            <tr>
                <td>排序</td>
                <td><input type="text" name="sort" value="0"></td>
                <td>填写一个数字</td>
            </tr>
        </table>
        <input class="btn" type="submit" name="submit" value="添加" style="margin-top: 20px;cursor: pointer"/>
    </form>
</div>

<?php include "inc/footer.inc.php";?>