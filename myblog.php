<?php
/**
 * Created by PhpStorm.
 * User: XY
 * Date: 2017/8/28
 * Time: 13:53
 */
try{
    $db = new PDO('mysql:host = localhost:dbname = blog','root','zfy5201314');
}catch (PDOException$error) {
    echo '链接数据库失败'.$error->getMessage();
    exit();
}
//发布 release
//new一个html文件，将数据传入数据库中
/**
<form action="myblog.php" method="post">
Content: <input type="text" name="content" />
Title: <input type="text" name="title" />
<input type="submit" />
</form>
 */
$content = $_POST['content'];
$title = $_POST['title'];
$result = $db->exec("insert into myblog ( content, title) values ('$_POST[content]','$_POST[title]')");
$result = $db->exec("insert into myblog values (null, null, '$content', '$title', now(), null, null, null)");
if ($result == 1) {
    return json([], '发布成功');
} else {
    return json([$db->errorInfo()], '发布失败', -1);
}

//获取 detail
$query = "select * from myblog";
$result = $db->query($query);
print_r($result);

//获取列表 list
$query = "select * from myblog";
$result = $db->query($query);
foreach($result as $value){
    echo $value['username'];
}

//删除 delete




//修改 update