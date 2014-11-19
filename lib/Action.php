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

    public function MakeJson(){
        $str = "{ \"actid\": \"".$this->actid."\", \"acttype\": \"".$this->acttype."\", \"acttypeid\":\""
            .$this->acttypeid."\", \"acttitle\": \"".$this->acttitle."\", \"actlink\": \"".$this->actlink."\", \"actdate\": \""
            .date("Y-m-d H:i:s", $this->actlink)."\", \"actvalid\": \"".$this->actvalid."\" }";
        return $str;
    }

    public static function Exists($name){
        if(!isset($name)) return true;
        $str = "select count(*) from Action where ";
        $paras = array();
        if(is_int($name)){
            $str .= "actid = :actid; ";
            $paras[":actid"] = $name;
        }
        else{
            $str .= "acttitle = :acttitle; ";
            $paras[":acttitle"] = $name;
        }
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($action){
        if(!is_a($action, "Action")) return false;
        $str = "insert into Action (acttype, acttypeid, acttitle, actlink, actvalid) values "
            ."(:acttype, :acttypeid, :acttitle, :actlink, :actvalid); ";
        $paras = array(
            ":acttype" => $action->acttype, ":acttypeid" => $action->acttypeid, ":acttitle" => $action->acttitle, 
            ":actlink" => $action->actlink, ":actvalid" => $action->actvalid
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function Update($action){
        if(!is_a($action, "Action")) return false;
        $str = "update Action set acttype = :acttype, acttypeid = :acttypeid, acttitle = :acttitle, "
            ."actlink = :actlink, actvalid = :actvalid where actid = :actid; ";
        $paras = array(
            ":acttype" => $action->acttype, ":acttypeid" => $action->acttypeid, ":acttitle" => $action->acttitle, 
            ":actlink" => $action->actlink, ":actvalid" => $action->actvalid, ":actid" => $action->actid
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function GetActions($type = "other", $typeid = -1, $valid = -1, $pagenum = 1, $pagesize = 0){
        $type = is_string($type) ? $type : "other";
        $typeid = is_int($typeid) ? $typeid : -1;
        $valid = is_int($valid) ? $valid : -1;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $count = "select count(*) as count ";
        $select = "select actid, acttype, acttypeid, acttitle, actlink, actdate, actvalid ";
        $where = "from Action where 1 = 1 ";
        $paras = array();
        if($type != "other"){
            $where .= "and acttype = :acttype ";
            $paras[":acttype"] = $type;
        }
        if($typeid < -1) $where .= "and acttypeid > 0 ";
        else if($typeid > -1){
            $where .= "and acttypeid = :acttypeid ";
            $paras[":acttypeid"] = $typeid
        }
        if($valid < -1) $where .= "and actvalid > 0 ";
        else if($valid > -1){
            $where .= "and actvalid = :actvalid ";
            $paras[":actvalid"] = $valid;
        }
        $count .= $where."; ";
        $where .= "order by actid desc ";
        $select .= $where;
        if($pagenum > 0 && $pagesize > 0)
            $select .= "limit ".($pagesize > 1 ? ($pagenum - 1) * $pagesize : 0).", ".$pagesize."; ";
        else $select .= "; ";
        $en = new Entity();
        $list = new Resaults();
        $res = $en->Query($count, $paras);
        if($res) $list->page->MakePage((int)$res[0]["count"], $pagenum, $pagesize);
        $res = $en->Query($select, $paras);
        if($res) foreach($res as $key => $value) $list->list[] = new Action($value);
        return $list;
    }

    public static function GetAction($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select actid, acttype, acttypeid, acttitle, actlink, actdate, actvalid "
            ."from Action where actid = :actid; ";
        $paras = array(":actid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
        return new Action($res);
    }

    public static function Valid($id, $valid = -1){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $valid = is_int($valid) ? $id : -1;
        $str = "update Action set ";
        $paras = array();
        if($valid == -1){
            $str .= "actvalid = case when actvalid = 0 then 1 else 1 end where actid = :actid; ";
            $paras = array(":actid" => $id);
        }
        else {
            $str .= "actvalid = :actvalid where actid = :actid; ";
            $paras = array(":actid" => $id, ":actvalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras);
    }
}
?>