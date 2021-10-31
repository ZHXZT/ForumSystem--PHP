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
if (basename($_SERVER['SCRIPT_NAME'])=='manage_add.php' || basename($_SERVER['SCRIPT_NAME'])=='manage.php'){
    if ($_SESSION['manage']['level']!='0'){
        if (!isset($_SERVER['HTTP_REFERER'])){
            $_SERVER['HTTP_REFERER']='index.php';
        }
        skip($_SERVER['HTTP_REFERER'], 'error', '你的权限不足！');
    }
}

?>
<?php include "inc/header.inc.php";?>

<div id="main" ">
<div class="title">管理员列表</div>
    <table class="list">
        <tr>
            <th>名称</th>
            <th>等级</th>
            <th>创建日期</th>
            <th>操作</th>
        </tr>
        <?php
        $query='select * from xzt_manage';
        $result=execute($link,$query);
        while ($data=mysqli_fetch_assoc($result)){
            if ($data['level']==0){
                $data['level']='超级管理员';
            }else{
                $data['level']='普通管理员';
            }
            $url=urlencode("manage_delete.php?id={$data['id']}");
            $return_url=urlencode($_SERVER['REQUEST_URI']);
            $delete_url="confirm.php?url={$url}&return_url={$return_url}";
            $html= <<<A

        <tr>
            <td>{$data['name']} [id:{$data['id']}]</td>
            <td>{$data['level']}</td>
            <td>{$data['create_time']}</td>
            <td><a href="{$delete_url}">[删除]</a></td>
        </tr>

A;
            echo $html;
        }
        ?>

    </table>
</div>
<?php include "inc/footer.inc.php";?>




