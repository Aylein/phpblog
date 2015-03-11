<?php
    include_once("../lib/User.php");
    include_once("../lib/Comment.php");

    if(!isset($_POST["action"])){
        echo Json::MakeJson(false, "no_action", "你想干什么 0 0~");
        die();
    }
    if(!isset($_POST["pass"])){
        echo Json::MakeJson(false, "no_pass", "需要一个密码");
        die();
    }
    $action = $_POST["action"];
    $pass = $_POST["pass"];
    $uuid = isset($_COOKIE["ao"]) ? $_COOKIE["ao"] : false;
    $user = false;
    if(!$uuid){
        $user = User::SignUp($pass);
        setCookie("ao", Commen::UUID());
    }
    else{
        $user = User::SignIn($pass);
    }
    if(!$user){
        echo json_encode(new Message(false, "no_user", "获取用户失败，如要更换用户，请清空当前cookie并在此输入密码。"));
        die();
    }
    $str = "";
    switch($action){
        case "newcom":
            $str = NewComment();
            break;
        case "getcomms":
            $str = GetComments();
            break;
        default: $str = Json::MakeJson(false, "no_action", "你想干什么 0 0~"); break;
    }
    echo $str;

    private function NewComment(){
        return Json::MakeJson(false);
    }

    private function GetComments(){
        return Json::MakeJson(false);
    }
?>