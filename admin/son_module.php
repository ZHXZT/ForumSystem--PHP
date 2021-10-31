<?php
include_once '../inc/mysql.inc.php';
include_once '../inc/config.inc.php';
include_once '../inc/tool.inc.php';
$link=connect();
//验证后台是否登录
if (!is_manage_login($link)){
    header('Location:login.php');
    exit();
}

if (isset($_POST['submit'])){
    foreach ($_POST['sort'] as $key=>$val){
        if (!is_numeric($val)||!is_numeric($key)){
            skip('son_module.php','error','排序参数错误！');
        }
        $query[]="update xzt_son_module set sort={$val} where id={$key}";
    }
    if (execute_multi($link,$query,$error)){
        skip('son_module.php','ok','排序修改成功！');
    }else{
        skip('son_module.php','error',$error);
    }
}
?>
<?php include "inc/header.inc.php";?>

<div id="main" ">
<div class="title">子板块列表</div>
<form method="post">
<table class="list">
    <tr>
        <th>排序</th>
        <th>版块名称</th>
        <th>父板块</th>
        <th>版主</th>
        <th>操作</th>
    </tr>
    <?php
    $query='select ssm.id,ssm.sort,ssm.module_name,sfm.module_name father_module_name,ssm.member_id from xzt_son_module ssm,xzt_father_module sfm where ssm.father_module_id=sfm.id order by sfm.id';
    $result=execute($link,$query);
    while ($data=mysqli_fetch_assoc($result)){
        $url=urlencode("son_module_delete.php?id={$data['id']}");
        $return_url=urlencode($_SERVER['REQUEST_URI']);
        $delete_url="confirm.php?url={$url}&return_url={$return_url}";
        $html= <<<A

        <tr>
            <td><input class="sort" type="text" name="sort[{$data['id']}]" value="{$data['sort']}"/></td>
            <td>{$data['module_name']}[id:{$data['id']}]</td>
            <td>{$data['father_module_name']}</td>
            <td>{$data['member_id']}</td>
            <td><a href="#">[访问]</a>&nbsp;&nbsp;<a href="son_module_update.php?id={$data['id']}">[编辑]</a>&nbsp;&nbsp;<a href="$delete_url">[删除]</a></td>
        </tr>

A;
        echo $html;
    }
    ?>
</table>
<input class="btn" type="submit" name="submit" value="排序" style="margin-top: 20px;cursor: pointer"/>
</form>
</div>
<?php include "inc/footer.inc.php";?>
