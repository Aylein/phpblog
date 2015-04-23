<?php
    include_once("../lib/User.php");
    include_once("../lib/Comment.php");
    include_once("../lib/Main.php");

    if(!isset($_POST["action"])){
        echo json_encode(new Message(false, "no_action", "你想干什么 0 0~"));
        exit();
    }
    if(!isset($_POST["pass"])){
        echo json_encode(new Message(false, "no_pass", "需要一个密码"));
        exit();
    }
    $action = $_POST["action"];
    $pass = $_POST["pass"];
    $name = isset($_COOKIE["ao"]) ? $_COOKIE["ao"] : null;
    $user = User::MakeUser($pass, $name)->obj;

    if($user == null){
        echo json_encode(new Message(false, "no_user", "获取用户或注册失败，如要更换用户，请清空当前cookie并在此输入密码。"));
        exit();
    }
    else setCookie("ao", $user->username, strtotime(date('Y-m-d H:i:s',strtotime("+ 1 year"))));

    $str = "";
    switch($action){
        default: $str = new Message(false, "no_action", "你想干什么 0 0~"); break;
    }
    echo json_encode($str);
?>