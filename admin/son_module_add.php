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
    if (!is_numeric($_POST['father_module_id'])){
        skip('son_module_add.php','error','所属父板块不得为空');
    }
    $query="select * from xzt_father_module where id={$_POST['father_module_id']}";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)==0){
        skip('son_module_add.php','error','所属父板块不存在');
    }
    if (empty($_POST['module_name'])){
        skip('son_module_add.php','error','板块名不得为空');
    }
    if (mb_strlen($_POST['module_name'])>66){
        skip('son_module_add.php','error','板块名不得多余66字符');
    }
    $_POST=escape($link,$_POST);
    $query="select * from xzt_son_module where module_name='{$_POST['module_name']}'";
    $result=execute($link,$query);
    if (mysqli_num_rows($result)){
        skip('son_module_add.php','error','该子板块已经存在');
    }
    if (mb_strlen($_POST['info'])>255){
        skip('son_module_add.php','error','简介不得多余255字符');
    }
    if (!is_numeric($_POST['sort'])){
        skip('son_module_add.php','error','排序只能为数字');
    }



    $query="insert into xzt_son_module(father_module_id,module_name,info,member_id,sort) values ({$_POST['father_module_id']},'{$_POST['module_name']}','{$_POST['info']}',{$_POST['member_id']},{$_POST['sort']})";
    execute($link,$query);
    if (mysqli_affected_rows($link)==1){
        skip('son_module_add.php','ok','添加成功');
    }else{
        skip('son_module_add.php','error','添加失败请重试');
    }
}
?>


<?php include "inc/header.inc.php";?>

    <div id="main">
        <div class="title" style="margin-bottom: 20px">添加子板块</div>
        <form method="post">
            <table class="au">
                <tr>
                    <td>所属板块</td>
                    <td>
                        <select name="father_module_id">
                            <option value="0">----请选择父板块----</option>
                            <?php
                            $query="select * from xzt_father_module";
                            $result_father=execute($link,$query);
                            while ($data_father=mysqli_fetch_assoc($result_father)){
                                echo "<option value='{$data_father['id']}'>{$data_father['module_name']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>必须选择一个所属的父板块</td>
                </tr>
                <tr>
                    <td>板块名称</td>
                    <td><input type="text" name="module_name"></td>
                    <td>板块名称不得为空</td>
                </tr>
                <tr>
                    <td>板块简介</td>
                    <td>
                        <textarea name="info"></textarea>
                    </td>
                    <td>板块简介不得多于255字</td>
                </tr>
                <tr>
                    <td>版主</td>
                    <td>
                        <select name="member_id">
                            <option value="0">----请选择一个版主----</option>
                        </select>
                    </td>
                    <td>选择一个会员作为版主</td>
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