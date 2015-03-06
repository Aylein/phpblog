<?php
    include("../lib/main.php");
    include("../lib/Json.php");
    include("../lib/Users.php");
    include("../lib/Type.php");

    //if(!isset($_SESSION["admin"])){
    //    echo Json::MakeJson(false, "no_login", "请先登录");
    //    die();
    //}

    if(!isset($_POST["action"])){
        echo Json::MakeJson(false, "no_action", "你想干什么 0 0~");
        die();
    }
    $action = $_POST["action"];
    $str = "";
    switch($action){
        case "newtype":
            $str = NewType();
            break;
        default: $str = Json::MakeJson(false, "no_action", "你想干什么 0 0~"); break;
    }
    echo $str;

    function NewType(){
        $type = new Type();
        $type->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
        $type->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : 0;
        $type->typeshow = isset($_POST["typeshow"]) && is_numeric($_POST["typeshow"]) ? (int)$_POST["typeshow"] : 0;
        $type->typename = isset($_POST["typename"]) ? $_POST["typename"] : "";
        $type->typesort = isset($_POST["typesort"]) && is_numeric($_POST["typesort"]) ? (int)$_POST["typesort"] : 0;
        $type->typevalid = isset($_POST["typevalid"]) && is_numeric($_POST["typevalid"]) ? (int)$_POST["typevalid"] : 0;
        if($type->typename == "") return Json::MakeJson(false, "no_typename", "类型名称不能为空");

        if($type->typeid != 0) { 
            if(Type::Update($type)) return Json::MakeJson(true, "ok", "修改成功！");
            else return Json::MakeJson(false, "update_error", "修改失败！");
        }
        else { 
            if(Type::Add($type)) return Json::MakeJson(true, "ok", "添加成功！");
            else return Json::MakeJson(false, "add_error", "添加失败！");
        }
    }
?>