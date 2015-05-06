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
    
    /*
    if(!isset($_SESSION["admin"])){
        echo json_encode(new Message("请先登录", false, null, "no_login"));
        die();
    }
    */
    if(!isset($_POST["_action"])){
        echo json_encode(new Message("你想干什么 0 0~", false, null, "no_action"));
        die();
    }
    $action = $_POST["_action"];

    $str = "";
    try{
        switch($action){
            case "getall": $str = json_encode(GetAll()); break;
            case "newtype": $str = json_encode(PostType()); break;
            case "post": $str = json_encode(Post()); break;
            case "valid": $str = json_encode(Valid()); break;
            default: $str = json_encode(new Message(false, "no_action", "你想干什么 0 0~")); break;
        }
    }catch(Exception $e){
        $str = new Message("something wrong", false, null, $e);
    }
    echo $str;

    function Get(){
        $type = isset($_POST["_type"]) ? $_POST["_type"] : "";
        $id = isset($_POST["_id"]) && is_numeric($_POST["_id"]) ? (int)$_POST["_id"] : 0;
        $deep = isset($_POST["_deep"]) && is_bool($_POST["_deep"]) ? (bool)$_POST["_deep"] : false;
        $value = null;
        switch($type){
            case "type": $value = Type::Get($id, $deep);
            case "user": $value = User::Get($id, $deep);
            case "stage": $value = Stage::Get($id, $deep);
            case "signon": $value = SignOn::Get($id, $deep);
            case "sign": $value = Sign::Get($id, $deep);
            case "doc": $value = Document::Get($id, $deep);
            case "com": $value = Comment::Get($id, $deep);
            case "action": $value = Action::Get($id, $deep);
            default: break;
        }
        return $value == null ? new Message("木有") : new Message("哈哈", true, $value);
    }

    function GetAll(){
        $type = isset($_POST["_type"]) ? $_POST["_type"] : "";
        $deep = isset($_POST["_deep"]) && is_bool($_POST["_deep"]) ? (bool)$_POST["_deep"] : false;
        $search = new stdClass();
        switch($type){
            case "type":
                $search->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : -2;
                $search->show = isset($_POST["show"]) && is_numeric($_POST["show"]) ? (int)$_POST["show"] : -1;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : -1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                return Type::GetAll($search, $deep);
            case "user": 
                $search->name = isset($_POST["name"]) ? strval($_POST["name"]) : "";
                $search->type = isset($_POST["type"]) ? strval($_POST["type"]) : "";
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                return User::GetAll($search, $deep);
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
            case "signon": 
                $search->signid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->userid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->type = isset($_POST["name"]) ? strval($_POST["typepid"]) : "";
                $search->typeid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                $search->order = isset($_POST["order"]) ? strval($_POST["order"]) : "";
                return SignOn::GetAll($search, $deep);
            case "sign": 
                $search->name = isset($_POST["name"]) ? strval($_POST["typepid"]) : "";
                $search->userid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                $search->order = isset($_POST["order"]) ? strval($_POST["order"]) : "";
                return Sign::GetAll($search, $deep);
            case "document": 
                $search->stgid = isset($_POST["stgid"]) && is_numeric($_POST["stgid"]) ? (int)$_POST["stgid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                return Document::GetAll($search, $deep);
            case "comment": 
                $search->type = isset($_POST["type"]) ? strval($_POST["type"]) : "other";
                $search->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
                $search->pid = isset($_POST["pid"]) && is_numeric($_POST["pid"]) ? (int)$_POST["pid"] : -2;
                $search->userid = isset($_POST["userid"]) && is_numeric($_POST["userid"]) ? (int)$_POST["userid"] : 0;
                $search->valid = isset($_POST["valid"]) && is_numeric($_POST["valid"]) ? (int)$_POST["valid"] : 1;
                $search->page = isset($_POST["page"]) && is_numeric($_POST["page"]) ? (int)$_POST["page"] : 0;
                $search->rows = isset($_POST["rows"]) && is_numeric($_POST["rows"]) ? (int)$_POST["rows"] : 0;
                $search->order = isset($_POST["name"]) ? strval($_POST["name"]) : "sort";
                return Comment::GetAll($search, $deep);
            case "action": 
                $search->type = isset($_POST["type"]) ? strval($_POST["type"]) : "other";
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

    function Post(){
        $type = isset($_POST["_type"]) ? $_POST["_type"] : "";
        switch ($type) {
            case 'type':
                $obj = new Type($_POST);
                if($obj->typename == "") return new Message("类型名称不能为空", false, null, "no_typename");
                return Type::Add_Update($obj);
            case "action":
                $obj = new Action($_POST);
                if($obj->acttype == "") return new Message("类别不能为空", false, null, "no_type");
                if($obj->acttitle == "") return new Message("标题不能为空", false, null, "no_title");
                return Action::Add_Update($obj);
            case "comment":
                $obj = new Comment($_POST);
                if($obj->comtype == "") return new Message("类别不能为空", false, null, "no_type");
                if($obj->userid < 1) return new Message("用户ID不能为空", false, null, "no_user");
                if($obj->comment == "") return new Message("评论内容不能为空", false, null, "no_comment");
                return Comment::Add_Update($obj);
            case "document":
                $obj = new Document($_POST);
                if($obj->stgid < 1) return new Message("文章ID不能为空", false, null, "no_stage");
                if($obj->doccontent == "") return new Message("文章内容不能为空", false, null, "no_document");
                return Document::Add_Update($obj);
            case "main":
                $obj = new Main($_POST);
                if($obj->_key == "") return new Message("键名不能为空", false, null, "no_key");
                if($obj->_value == "") return new Message("键值不能为空", false, null, "no_value");
                return Main::Add_Update($obj);
            case "sign":
                $obj = new Sign($_POST);
                if($obj->signname == "") return new Message("标签名不能为空", false, null, "no_name");
                if($obj->userid < 1) return new Message("用户ID不能为空", false, null, "no_user");
                return Sign::Add_Update($obj);
            case "signon":
                $obj = new SignOn($_POST);
                if($obj->signid < 1) return new Message("标签ID不能为空", false, null, "no_sign");
                if($obj->userid < 1) return new Message("用户ID不能为空", false, null, "no_user");
                if($obj->sotype == "") return new Message("类别不能为空", false, null, "no_type");
                if($obj->sotypeid < 1) return new Message("类别ID不能为空", false, null, "no_typeid");
                return SignOn::Add_Update($obj);
            case "stage":
                $obj = new Stage($_POST);
                if($obj->typeid < 1) return new Message("分类ID不能为空", false, null, "no_type");
                if($obj->userid < 1) return new Message("用户ID不能为空", false, null, "no_user");
                if($obj->stgtitle == "") return new Message("标题不能为空", false, null, "no_title");
                return Stage::Add_Update($obj);
            case "user":
                $now = date("H");
                $obj = new User($_POST);
                $obj->username = $obj->userid < 1 ? Commen::Rand(3)."#".Commen::Rand(5).".".chr($now + 65).chr($now + 97) : "";
                if($obj->username == "") return new Message("用户名不能为空", false, null, "no_name");
                if($obj->userpass == "") return new Message("用户密码不能为空", false, null, "no_pass");
                return User::Add_Update($obj);
            default: return new Message("post ...  what ...");
        }
    }

    function Valid(){
        $type = isset($_POST["_type"]) ? $_POST["_type"] : "";
        $id = isset($_POST["_id"]) && is_numeric($_POST["_id"]) ? (int)$_POST["_id"] : 0;
        $valid = isset($_POST["_valid"]) && is_numeric($_POST["_valid"]) ? (int)$_POST["_valid"] : null;
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