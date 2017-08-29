<?php
/**
 * Created by PhpStorm.
 * User: XY
 * Date: 2017/8/28
 * Time: 13:53
 */
try{
    $db = new PDO('mysql:host = localhost:dbname = reg','root','zfy5201314');
}catch (PDOException$error) {
    echo '链接数据库失败'.$error->getMessage();
    exit();
}
//获取接口名称
$action = empty($_POST['action'])?'':trim($_POST['action']);

function json ($data = [], $msg = 'success', $code = 0, $exit = true) {
    $ret = json_encode(
        [
            'data' => $data,
            'msg' => $msg,
            'code' => $code
        ],
        JSON_UNESCAPED_UNICODE
    );
    if ($exit) {
        exit($ret);
    } else {
        return $ret;
    }
}

// 执行操作
if ($action == 'register') {
    // 注册操作
    // todo 数据验证...

    // 获取表单数据k
    $user_name = $_POST['user_name'];
 //   $password = $_POST['password'];

    // todo 验证用户名是否被使用
    function name($user_name)
    {
        if (empty($user_name)) return false; //是否已存在
        return true;
    }

    // todo password 处理成密文
    $password = md5($_POST['password']);

    $result = $db->exec("insert into users values (null, '$user_name', '$password', null, null)");
    if ($result == 1) {
        return json([], '注册成功');
    } else {
        return json([$db->errorInfo()], '注册失败', -1);
    }
} elseif ($action == 'login') {
    // 登陆操作
    $sql = "select * from users where user_name='$user_name' and password='$password'";
    $land = $db->query($sql);
    $info = $land->fetch(PDO::FETCH_ASSOC);
    return json(['password' => $password, 'user_name' => $user_name], '登陆成功');
}else {
        // 密码错误
         return json([], '用户名或密码错误', -1);
    }
elseif($action == 'get_self_info') {

//获取自己信息
if (empty($uid) || empty($token)) return json([], '未登录', -1);
    // 验证token是否正确
    $result = $db->query("select * from `users` where `id` = $uid and `token` = '$token'");
    if ($result) {
        $user = $result->fetch($db::FETCH_ASSOC);

        // 查询不到
        if (empty($result)) return json([], '未登录', -1);

        // 查询到了
        unset($user['password'], $user['token']);
        return json(['item' => $user]);
    } else {
           return json([$db->errorInfo()], '数据库错误', -1);
    }

    elseif($action == 'get_user_info') {
    // 根据user_id 获取用户信息
    $user_id = empty($_POST['user_id'])?null:trim($_POST['user_id']);
    if (empty($user_id)) return json([], 'user_id必须传入', -1);
    if (!is_numeric($user_id)) return json([], 'user_id必须为参数', -1);

// 执行查询
$result = $db->query("select `id`, `user_name`, `reg_time` from `users` where `id` = $user_id");
if ($result) {
          $user = $result->fetch($db::FETCH_ASSOC);
         return json(['item' => $user]);
   } else {
           return json([$db->errorInfo()], '数据库错误', -1);
   }
 } else {
      return json([$_SERVER], '无效的接口', -1);