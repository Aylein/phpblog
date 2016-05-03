<?php
include_once("Entity.php");
include_once("User.php");
class Comment{
    var $comid; // int primary key auto_increment,
    var $comtype; // nvarchar(15) not null, //commt docut stage
    var $comtypeid; // int not null,
    var $compid; // int default 0,
    var $userid;
    var $repeatid; // nvarchar(12) not null,
    var $repeatname;
    var $comdate; // timestamp default current_timestamp,
    var $comment; // nvarchar(450) not null,
    var $comsort; // int default 0,
    var $comvalid; // int default 1

    var $user;
    var $repeat;

    public function __construct($array = null, $bo = false){
        if($array == null || !is_array($array)){
            $this->comid = 0;
            $this->comtype = "";
            $this->comtypeid = 0;
            $this->compid = 0;
            $this->userid = 0;
            $this->repeatid = 0;
            $this->repeatname = "";
            $this->comdate = -1;
            $this->comment = "";
            $this->comsort = 0;
            $this->comvalid = 0;
        }
        else{
            $this->comid = isset($array["comid"]) && is_numeric($array["comid"]) ? (int)$array["comid"] : 0;
            $this->comtype = isset($array["comtype"]) ? $array["comtype"] : "";
            $this->comtypeid = isset($array["comtypeid"]) && is_numeric($array["comtypeid"]) ? (int)$array["comtypeid"] : 0;
            $this->compid = isset($array["compid"]) && is_numeric($array["compid"]) ? (int)$array["compid"] : 0;
            $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
            $this->repeatid = isset($array["repeatid"]) && is_numeric($array["repeatid"]) ? (int)$array["repeatid"] : 0;
            $this->repeatname = isset($array["repeatname"]) ? $array["repeatname"] : "";
            $this->comdate = isset($array["comdate"])  ? $array["comdate"] : "";
            $this->comment = isset($array["comment"]) ? $array["comment"] : "";
            $this->comsort = isset($array["comsort"]) && is_numeric($array["comsort"]) ? (int)$array["comsort"] : 0;
            $this->comvalid = isset($array["comvalid"]) && is_numeric($array["comvalid"]) ? (int)$array["comvalid"] : 0;
        }
        $this->user = $bo && $this->userid > 0 ? User::Get($this->userid) : null;
        $this->repeat = $bo && $this->repeatid > 0 ? User::Get($this->repeatid) : null;
    }

    public static function Add($com){
        if(!$com instanceof Comment) return new Message("对象类型不正确");
        $str = "insert into Comments (comtype, comtypeid, compid, userid, repeatid, repeatname, comment, comsort, comvalid) values "
            ."(:comtype, :comtypeid, :compid, :userid, :repeatid, :repeatname, :comment, :comsort, :comvalid); ";
        $str .= "select comid, comtype, comtypeid, compid, comdate, comment, comsort, comvalid "
            ."from Comments where comid = @@identity; ";
        $paras = array(
            ":comtype" => $com->comtype, ":comtypeid" => $com->comtypeid, ":compid" => $com->compid, ":userid" => $com->userid, ":repeatid" => $com->repeatid, 
            ":repeatname" => $com->repeatname, ":comment" => $com->comment, ":comsort" => $com->comsort, ":comvalid" => $com->comvalid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Comment($en[1][0])) : new Message("添加失败");
    }

    public static function Update($com){
        if(!$com instanceof Comment) return new Message("对象类型不正确");
        $str = "update Comments set comtype = :comtype, comtypeid = :comtypeid, compid = :compid, repeatid = :repeatid, "
            ."repeatname = :repeatname, comment = :comment, comsort = :comsort, comvalid = :comvalid where comid = :comid; ";
        $str .= "select comid, comtype, comtypeid, compid, comdate, comment, comsort, comvalid "
            ."from Comments where comid = :comid; ";
        $paras = array(
            ":comtype" => $com->comtype, ":comtypeid" => $com->comtypeid, ":compid" => $com->compid, ":repeatid" => $com->repeatid, 
            ":repeatname" => $com->repeatname, ":comment" => $com->comment, ":comsort" => $com->comsort, ":comvalid" => $com->comvalid, 
            ":comid" => $com->comid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new Comment($en[1][0], $deep)) : new Message("修改失败");
    }

    public static function Add_Update($com){
        if(!$com instanceof Comment) return new Message("对象类型不正确");
        return $com->comid > 0 ? Comment::Update($com) : Comment::Add($com);
    }

    public static function Count($search = null){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->type = isset($search->type) ? strval($search->type) : "other";
        $search->typeid = isset($search->typeid) && is_numeric($search->typeid) ? (int)$search->typeid : 0;
        $search->pid = isset($search->pid) && is_numeric($search->pid) ? (int)$search->pid : -2;
        $search->userid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $count = "select count(*) as count ";
        $where = "from Comments where 1 = 1 ";
        $paras = array();
        if($search->type != "other"){
            $where .= "and comtype = :comtype ";
            $paras[":comtype"] = $search->type;
        }
        if($search->typeid > 0){
            $where .= "and comtypeid = :comtypeid ";
            $paras[":comtypeid"] = $search->typeid;
        }
        if($search->pid = -1) $where .= "and compid > 0";
        else if($search->pid > -1){
            $where .= "and compid = :compid";
            $paras[":compid"] = $search->pid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and comvalid = :comvalid ";
            $paras[":comvalid"] = $search->valid;
        }
        $count .= $where.";";
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 1 || count($res[0]) != 1) return 0;
        return (int)$res[0][0]["count"];
    }

    public static function GetAll($search = null, $deep = false){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->type = isset($search->type) ? strval($search->type) : "other";
        $search->typeid = isset($search->typeid) && is_numeric($search->typeid) ? (int)$search->typeid : 0;
        $search->pid = isset($search->pid) && is_numeric($search->pid) ? (int)$search->pid : -2;
        $search->userid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $search->order = isset($search->name) ? strval($search->name) : "sort";
        $count = "select count(*) as count ";
        $select = "select comid, comtype, comtypeid, compid, userid, repeatid, repeatname, comdate, comment, comsort, comvalid ";
        $where = "from Comments where 1 = 1 ";
        $paras = array();
        if($search->type != "other"){
            $where .= "and comtype = :comtype ";
            $paras[":comtype"] = $search->type;
        }
        if($search->typeid > 0){
            $where .= "and comtypeid = :comtypeid ";
            $paras[":comtypeid"] = $search->typeid;
        }
        if($search->pid == -1) $where .= "and compid > 0 ";
        else if($search->pid > -1){
            $where .= "and compid = :compid ";
            $paras[":compid"] = $search->pid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and comvalid = :comvalid ";
            $paras[":comvalid"] = $search->valid;
        }
        $count .= $where."; ";
        $where .= "order by comid desc ";
        $select .= $where;
        if($search->page > 0 && $search->rows > 0){
            $select .= "limit :page, :rows; ";
            $paras[":page"] = ($search->page - 1) * $search->rows;
            $paras[":rows"] = $search->rows;
        }
        else $select .= "; ";
        $count .= $select;
        $list = array();
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 2 || count($res[0]) != 1) return new Resaults();
        foreach($res[1] as $key => $value) $list[] = new Comment($value, $deep);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id, $deep = false){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select comid, comtype, comtypeid, compid, userid, repeatid, repeatname, comdate, comment, comsort, comvalid "
            ."from Comments where comid = :comid; ";
        $paras = array(":comid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return null;
        return new Comments($res, $deep);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Comments set ";
        $paras = array();
        if(!$valid){
            $str .= "comvalid = case when comvalid = 0 then 1 else 1 end where comid = :comid; ";
            $paras = array(":comid" => $id);
        }
        else {
            $str .= "comvalid = :comvalid where comid = :comid; ";
            $paras = array(":comid" => $id, ":comvalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }
}
?>