<?php
    Session_start();
    include("../lib/Main.php");
    include("../lib/User.php");
    include("../lib/Type.php");

    if(!isset($_POST["action"])){
        echo json_encode(new Message(false, "no_action", "你想干什么 0 0~"));
        die();
    }
    $action = $_POST["action"];

    $str = "";
    try{
        switch($action){
            case "signin": $str = json_encode(SignIn()); break;
            case "issigned": $str = json_encode(Sessions()); break;
            case "gettypes": $str = json_encode(GetTypes()); break;
            case "gettype": $str = json_encode(GetType_byID()); break;
            default: $str = json_encode(new Message(false, "no_action", "你想干什么 0 0~")); break;
        }
    }catch(Exception $e){
        $str = new Message(false, "something wrong", $e);
    }
    echo $str;

    function Sessions(){
        return isset($_SESSION["admin"]) ? new Message(true, "yes") : new Message(false, "no");
    }

    function SignIn(){
        $name = isset($_POST["user"]) ? trim($_POST["user"]) : "";
        $pass = isset($_POST["pass"]) ? trim($_POST["pass"]) : "";
        $res = User::SignIn(md5($name." ".$pass));
        if($res->res) {
            if($res->obj->usertype == "admin") {
                $res->code = "session";
                $_SESSION["admin"] = $res->obj;
            }
            else $res->code = "cookie";
            setCookie("ao", $res->obj->username, strtotime(date('Y-m-d H:i:s',strtotime("+ 1 year"))));
        }
        return $res;
    }

    function GetTypes(){
        $typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : -1;
        $show = isset($_POST["show"]) && is_numeric($_POST["show"]) ? (int)$_POST["show"] : -1;
        $valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : -1;
        $page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
        $pagenum = isset($_POST["pagenum"]) && is_numeric($_POST["pagenum"]) ? (int)$_POST["pagenum"] : 0;
        return Type::GetTypes($typepid, $show, $valid, $page, $pagenum);
    }

    function GetType_byID(){
        $typeid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : 0;
        //if($typeid < 1) return new Message(false, "no_typeid", "无效的类型ID");
        $res = Type::GetType($typeid);
        return !$res ? new Message(false, "no_type", "没有找到指定的类型") : $res;
        return new Type();
    }

    /*
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
    */
?>