<?php
    include_once("../lib/User.php");
    include_once("../lib/Comment.php");
    include_once("../lib/Main.php");

    if(!isset($_POST["action"])){
        echo json_encode(new Message("你想干什么 0 0~", false, null, "no_action"));
        exit();
    }
    if(!isset($_POST["pass"])){
        echo json_encode(new Message("需要一个密码", false, null, "no_pass"));
        exit();
    }
    $action = $_POST["action"];
    $pass = $_POST["pass"];
    $name = isset($_COOKIE["ao"]) ? $_COOKIE["ao"] : null;
    $user = User::MakeUser($pass, $name)->obj;

    if($user == null){
        echo json_encode(new Message("获取用户或注册失败，如要更换用户，请清空当前cookie并输入一个新密码。", false, null, "no_user"));
        exit();
    }
    else setCookie("ao", $user->username, strtotime(date('Y-m-d H:i:s',strtotime("+ 1 year"))));

    $str = "";
    switch($action){
        default: $str = new Message("你想干什么 0 0~", false, null, "no_action"); break;
    }
    echo json_encode($str);
?>