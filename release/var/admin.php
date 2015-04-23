<?php
    include("../lib/Main.php");
    include("../lib/Type.php");

    if(!isset($_SESSION["admin"])){
        echo json_encode(new Message(false, "no_login", "请先登录"));
        die();
    }
    if(!isset($_POST["action"])){
        echo json_encode(new Message(false, "no_action", "你想干什么 0 0~"));
        die();
    }
    $action = $_POST["action"];

    $str = "";
    try{
        switch($action){
            case "newtype": $str = json_encode(PostType()); break;
            default: $str = json_encode(new Message(false, "no_action", "你想干什么 0 0~")); break;
        }
    }catch(Exception $e){
        $str = new Message(false, "something wrong", $e);
    }
    echo $str;

    function PostType(){
        $type = new Type();
        $type->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
        $type->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : 0;
        $type->typeshow = isset($_POST["typeshow"]) && is_numeric($_POST["typeshow"]) ? (int)$_POST["typeshow"] : 0;
        $type->typename = isset($_POST["typename"]) ? $_POST["typename"] : "";
        $type->typesort = isset($_POST["typesort"]) && is_numeric($_POST["typesort"]) ? (int)$_POST["typesort"] : 0;
        $type->typevalid = isset($_POST["typevalid"]) && is_numeric($_POST["typevalid"]) ? (int)$_POST["typevalid"] : 0;
        if($type->typename == "") return new Message(false, "no_typename", "类型名称不能为空");
        return Type::Add_Update($type);
    }

    function Valid(){
        $type = isset($_POST["type"]) ? $_POST["type"] : "";
        $id = isset($_POST["id"]) && is_numeric($_POST["id"]) ? (int)$_POST["id"] : 0;
        $valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : null;
        switch($type){
            case "type": return Type::Valid($id, $valid);
            case "user": return User::Valid($id, $valid);
            case "stage": return Stage::Valid($id, $valid);
            case "signon": return SignOn::Valid($id, $valid);
            case "sign": return Sign::Valid($id, $valid);
            case "doc": return Document::Valid($id, $valid);
            case "com": return Comment::Valid($id, $valid);
            case "action": return Action::Valid($id, $valid);
            default: return new Message("嗯。。。");
        }
    }
?>