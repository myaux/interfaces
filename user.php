<?php
/**
 * Created by PhpStorm.
 * User: XY
 * Date: 2017/8/28
 * Time: 13:53
 */
try{
    $db = new PDO('mysql:host=localhost;dbname=blog','root','zfy5201314');
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

    // 获取表单数据
    //判断变量是否为空
    $user_name = empty($_POST['user_name']) ? null : trim($_POST['user_name']);
    $password = empty($_POST['password']) ? null : trim($_POST['password']);

    $user_nameLen = strlen($user_name);
    $passwordLen = strlen($password);

    if ($user_nameLen < 6) {
        exit('用户名长度不能小于6');
    } elseif ($passwordLen < 6) {
        exit('密码长度不能小于6');
    };

    //验证用户名是否已存在
    $result = $db->query("select * from `users` where `user_name`='$user_name'");
    if ($result->fetchObject()) return json([], '用户名已被使用', -1);

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $result = $db->exec("insert into `users` (`user_name`,`password`) values ('$user_name','$hash_password')");
    if ($result == 1) {
        return json([], '注册成功');
    } else{
        return json([$db->errorInfo()], '注册失败', -1);
    }

}
if ($action = 'login') {
    $user_name = empty($_POST['user_name']) ? null : trim($_POST['user_name']);
    $password = empty($_POST['password']) ? null : trim($_POST['password']);
    if (empty($user_name)) return json([], '用户名不能为空', -1);
    if (empty($password)) return json([], '密码不能为空', -1);

    $result1 = $db->query("select * from `users` where `user_name`='$user_name'");
    $log = $result1->fetchObject();
    if (empty($log)) return json([], '用户名不存在');
    $hash_password = $log->password;
    $user_id = $log->id;

    if (password_verify($password, $hash_password)) {
        $token = password_hash($password, $hash_password);
        $only = $db->query("update users set token = '$token' where id = '$user_id'");
        if ($only < 1) return json([], 'token创建失败', -1);
        return json(['user_name = $user_name', 'password = $hash_password'], '登陆成功');
    }else {
        return json([],'用户名或者密码错误');
    }
}
if ($action = 'get_self_info'){
    $is_login = get_login_status($db,$uid);
    if (!$is_login) return json([],'未登录',-1);
    $result= $db->query("select * from `user` where id = '$uid'");
    if ($result){
        $user = $result->fetch($db::FETCH_ASSOC);
        unset($user['password'],$user['token']);
        return json(['item'=>$user]);
    }else{
        return json([$db->errorInfo()],'查询失败',-1);
    }

}
//登陆 login
//注册 register
//获取自己信息 get_self_info
//获取用户信息 get_user_info























