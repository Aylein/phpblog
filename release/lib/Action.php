<?php
include_once("Entity.php");
class Action{
    var $actid; // int primary key auto_increment,
    var $acttype; // nvarchar(15) not null,
    var $acttypeid;
    var $acttitle; // nvarchar(25) not null,
    var $actlink; // nvarchar(50),
    var $actdate; // timestamp default current_timestamp
    var $actvalid;

    public function __construct($array = null){
        if(!isset($array) || !is_array($array)){
            $this->actid = 0;
            $this->acttype = "";
            $this->acttypeid = 0;
            $this->acttitle = "";
            $this->actlink = "";
            $this->actdate = -1;
            $this->actdate = 0;
        }
        else{
            $this->actid = isset($array["actid"]) && is_numeric($array["actid"]) ? (int)$array["actid"] : 0;
            $this->acttype = isset($array["acttype"]) ? $array["acttype"] : "";
            $this->acttypeid = isset($array["acttypeid"]) && is_numeric($array["acttypeid"]) ? (int)$array["acttypeid"] : 0;
            $this->acttitle = isset($array["acttitle"]) ? $array["acttitle"] : "";
            $this->actlink = isset($array["actlink"]) ? $array["actlink"] : "";
            $this->actdate = isset($array["actdate"]) ? strtotime($array["actdate"]) : -1;
            $this->actvalid = isset($array["actvalid"]) && is_numeric($array["actvalid"]) ? (int)$array["actvalid"] : 0;
        }
    }

    public static function Add($action){
        if(!$action instanceof Action) return new Message("对象类型不正确");
        $str = "insert into Action (acttype, acttypeid, acttitle, actlink, actvalid) values "
            ."(:acttype, :acttypeid, :acttitle, :actlink, :actvalid); ";
        $str .= "select actid, acttype, acttypeid, acttitle, actlink, actdate, actvalid "
            ."from Action where actid = @@identity; ";
        $paras = array(
            ":acttype" => $action->acttype, ":acttypeid" => $action->acttypeid, ":acttitle" => $action->acttitle, 
            ":actlink" => $action->actlink, ":actvalid" => $action->actvalid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Action($en[1][0])) : new Message("添加失败");
    }

    public static function Update($action){
        if(!$action instanceof Action) return new Message("对象类型不正确");
        $str = "update Action set acttype = :acttype, acttypeid = :acttypeid, acttitle = :acttitle, "
            ."actlink = :actlink, actvalid = :actvalid where actid = :actid; ";
        $paras = array(
            ":acttype" => $action->acttype, ":acttypeid" => $action->acttypeid, ":acttitle" => $action->acttitle, 
            ":actlink" => $action->actlink, ":actvalid" => $action->actvalid, ":actid" => $action->actid
        );
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true, $action) : new Message("修改失败");
    }

    public static function Add_Update($action){
        if(!$action instanceof Action) return new Message("对象类型不正确");
        return $action->actid > 0 ? Action::Update($action) : Action::Add($action);
    }

    public static function GetAll($search){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->type = isset($search->type) ? strval($search->type) : "other";
        $search->typeid = isset($search->typeid) && is_numeric($search->typeid) ? (int)$search->typeid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $count = "select count(*) as count ";
        $select = "select actid, acttype, acttypeid, acttitle, actlink, actdate, actvalid ";
        $where = "from Action where 1 = 1 ";
        $paras = array();
        if($search->type != "other"){
            $where .= "and acttype = :acttype ";
            $paras[":acttype"] = $search->type;
        }
        if($search->typeid > 0){
            $where .= "and acttypeid = :acttypeid ";
            $paras[":acttypeid"] = $search->typeid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and actvalid = :actvalid ";
            $paras[":actvalid"] = $search->valid;
        }
        $count .= $where."; ";
        $where .= "order by actid desc ";
        $select .= $where;
        if($pagenum > 0 && $pagesize > 0){
            $select .= "limit :page, :rows; ";
            $paras[":page"] = ($search->page - 1) * $search->rows;
            $paras[":rows"] = $search->rows;
        }
        else $select .= "; ";
        $list = array();
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 2 || count($res[0]) != 1) return new Resaults();
        foreach($res[1] as $key => $value) $list[] = new Action($value);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select actid, acttype, acttypeid, acttitle, actlink, actdate, actvalid "
            ."from Action where actid = :actid; ";
        $paras = array(":actid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return null;
        return new Action($res);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Action set ";
        $paras = array();
        if(!$valid){
            $str .= "actvalid = case when actvalid = 0 then 1 else 1 end where actid = :actid; ";
            $paras = array(":actid" => $id);
        }
        else {
            $str .= "actvalid = :actvalid where actid = :actid; ";
            $paras = array(":actid" => $id, ":actvalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }
}
?>