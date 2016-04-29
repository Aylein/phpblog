<?php
    Session_start();
    include_once("../lib/User.php");
    include_once("../lib/Comment.php");
    include_once("../lib/Main.php");

    if(!isset($_POST["_action"])){
        echo json_encode(new Message("你想干什么 0 0~", false, null, "no_action"));
        exit();
    }
    if(!isset($_POST["_pass"])){
        echo json_encode(new Message("需要一个密码", false, null, "no_pass"));
        exit();
    }
    $action = $_POST["_action"];
    $pass = $_POST["_pass"];
    $name = isset($_COOKIE["ao"]) ? $_COOKIE["ao"] : null;
    $msg = User::MakeUser($pass, $name);
    $user = $msg->obj;
    
    if(!$msg->res || $user == null){
        echo json_encode(new Message("获取用户或注册失败，如要更换用户，请清空当前cookie并输入一个新密码。", false, null, "no_user"));
        exit();
    }
    else setCookie("ao", $user->username, strtotime(date('Y-m-d H:i:s', strtotime("+ 1 year"))));

    $str = "";
    try{
        switch($action){
            case "post": $str = json_encode(Post($user)); break;
            default: $str = json_encode(new Message(false, "no_action", "你想干什么 0 0~")); break;
        }
    }catch(Exception $e){
        $str = new Message("something wrong", false, null, $e);
    }
    echo $str;    

    function Post($user){
        $type = isset($_POST["_type"]) ? $_POST["_type"] : "";
        switch ($type) {
            case "comment":
                $obj = new Comment($_POST);
                $obj->userid = $user->userid;
                if($obj->comtype == "") return new Message("类别不能为空", false, null, "no_type");
                if($obj->userid < 1) return new Message("用户ID不能为空", false, null, "no_user");
                if($obj->comment == "") return new Message("评论内容不能为空", false, null, "no_comment");
                print_r($obj); die();
                return Comment::Add_Update($obj);
            default: return new Message("post ...  what ...");
        }
    }
?>