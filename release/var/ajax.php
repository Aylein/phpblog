<?php
    Session_start();
    include_once("../lib/Main.php");
    include_once("../lib/Action.php");
    include_once("../lib/Comment.php");
    include_once("../lib/Document.php");
    include_once("../lib/Sign.php");
    include_once("../lib/SignOn.php");
    include_once("../lib/Stage.php");
    include_once("../lib/Type.php");
    include_once("../lib/User.php");

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
        $deep = isset($_POST["deep"]) ? (bool)$_POST["deep"] : false;
        $search = new stdClass();
        switch($type){
            case "type":
                $search->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : -1;
                $search->show = isset($_POST["show"]) && is_numeric($_POST["show"]) ? (int)$_POST["show"] : -1;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : -1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                return Type::GetAll($search, $deep);
            case "stage": 
                $search->title = isset($_POST["title"]) ? strval($_POST["title"]) : "";
                $search->subtitle = isset($_POST["subtitle"]) ? strval($_POST["subtitle"]) : "";
                $search->stgpid = isset($_POST["stgpid"]) && is_numeric($_POST["stgpid"]) ? (int)$_POST["stgpid"] : 0;
                $search->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->stagenim = isset($_POST["stagenim"]) && is_numeric($_POST["stagenim"]) ? (int)$_POST["stagenim"] : 0;
                $search->stagemax = isset($_POST["stagemax"]) && is_numeric($_POST["stagemax"]) ? (int)$_POST["stagemax"] : 0;
                $search->viewmin = isset($_POST["viewmin"]) && is_numeric($_POST["viewmin"]) ? (int)$_POST["viewmin"] : 0;
                $search->viewmax = isset($_POST["viewmax"]) && is_numeric($_POST["viewmax"]) ? (int)$_POST["viewmax"] : 0;
                $search->commmin = isset($_POST["commmin"]) && is_numeric($_POST["commmin"]) ? (int)$_POST["commmin"] : 0;
                $search->commmax = isset($_POST["commmax"]) && is_numeric($_POST["commmax"]) ? (int)$_POST["commmax"] : 0;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                $search->$order = isset($_POST["order"]) ? strval($_POST["order"]) : "";
                return Stage::GetAll($search, $deep);
            case "sign": 
                $search->name = isset($_POST["name"]) ? strval($_POST["typepid"]) : "";
                $search->userid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                $search->order = isset($_POST["order"]) ? strval($_POST["order"]) : "";
                return Sign::GetAll($search, $deep);
            case "comment": 
                $search->type = isset($_POST["mtype"]) ? strval($_POST["mtype"]) : "other";
                $search->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
                $search->pid = isset($_POST["pid"]) && is_numeric($_POST["pid"]) ? (int)$_POST["pid"] : -2;
                $search->userid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                $search->order = isset($_POST["name"]) ? strval($_POST["name"]) : "sort";
                return Comment::GetAll_forEveryAll($search, $deep);
            case "action": 
                $search->type = isset($_POST["mtype"]) ? strval($_POST["mtype"]) : "other";
                $search->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                return Action::GetAll($search, $deep);
            case "main": 
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                return Main::GetAll($search, $deep);
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
            case "user": $value = User::Get($id, $deep);
            case "stage": $value = Stage::Get($id, $deep);
            case "sign": $value = Sign::Get($id, $deep);
            case "document": $value = Document::Get($id, $deep);
            default: break;
        }
        return $value == null ? new Message("木有") : new Message("哈哈", true, $value);
    }
?>