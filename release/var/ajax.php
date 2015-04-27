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
            case "getall": $str = json_encode(GetAll()); break;
            case "get": $str = json_encode(Get()); break;
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
        $search = new stdClass();
        $search->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : -1;
        $search->show = isset($_POST["show"]) && is_numeric($_POST["show"]) ? (int)$_POST["show"] : -1;
        $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : -1;
        $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
        $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
        return Type::GetAll($search);
    }

    function GetAll(){
        $type = isset($_POST["type"]) ? $_POST["type"] : "";
        $deep = isset($_POST["deep"]) && is_bool($_POST["deep"]) ? (bool)$_POST["deep"] : false;
        $search = new stdClass();
        switch($type){
            case "type":
                $search->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : -1;
                $search->show = isset($_POST["show"]) && is_numeric($_POST["show"]) ? (int)$_POST["show"] : -1;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : -1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                return Type::GetAll($search, $deep);
            case "user": 
                return User::GetAll($search, $deep);
            case "stage": 
                return Stage::GetAll($search, $deep);
            case "signon": 
                return SignOn::GetAll($search, $deep);
            case "sign": 
                return Sign::GetAll($search, $deep);
            case "doc": 
                return Document::GetAll($search, $deep);
            case "com": 
                return Comment::GetAll($search, $deep);
            case "action": 
                return Action::GetAll($search, $deep);
            default: return new Resaults();
        }
    }

    function Get(){
        $type = isset($_POST["type"]) ? $_POST["type"] : "";
        $id = isset($_POST["id"]) && is_numeric($_POST["id"]) ? (int)$_POST["id"] : 0;
        $deep = isset($_POST["deep"]) && is_bool($_POST["deep"]) ? (bool)$_POST["deep"] : false;
        $value = null;
        switch($type){
            case "type": $value = Type::Get($id, $deep);
            case "user": $value = User::Valid($id, $deep);
            case "stage": $value = Stage::Valid($id, $deep);
            case "signon": $value = SignOn::Valid($id, $deep);
            case "sign": $value = Sign::Valid($id, $deep);
            case "doc": $value = Document::Valid($id, $deep);
            case "com": $value = Comment::Valid($id, $deep);
            case "action": $value = Action::Valid($id, $deep);
            default: break;
        }
        return $value == null ? new Message("木有") : new Message("哈哈", true, $value);
    }
?>