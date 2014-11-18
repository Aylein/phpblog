<?php
include_once("Entity.php");
class Comment{
    var $comid; // int primary key auto_increment,
    var $comtype; // nvarchar(15) not null,
    var $comtypeid; // int not null,
    var $compid; // int default 0,
    var $comname; // nvarchar(12) not null,
    var $comrename;
    var $comdate; // timestamp default current_timestamp,
    var $comment; // nvarchar(450) not null,
    var $comsort; // int default 0,
    var $comvalid; // int default 1

    public function __construct($array = null){
        if($array == null || !is_array($array)){
            $this->comid = 0;
            $this->comtype = "";
            $this->comtypeid = 0;
            $this->compid = 0;
            $this->comname = "";
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
            $this->comname = isset($array["comname"]) ? $array["comname"] : "";
            $this->comrename = isset($array["comrename"]) ? $array["comrename"] : "";
            $this->comdate = isset($array["comdate"])  ? strtotime($array["comdate"]) : -1;
            $this->comment = isset($array["comment"]) ? $array["comment"] : "";
            $this->comsort = isset($array["comsort"]) && is_numeric($array["comsort"]) ? (int)$array["comsort"] : 0;
            $this->comvalid = isset($array["comvalid"]) && is_numeric($array["comvalid"]) ? (int)$array["comvalid"] : 0;
        }
    }

    public function MakeJson(){
        $str = "{ \"comid\": \"".$this->comid."\", \"comtype\": \"".$this->comtype."\", \"comtypeid\": \"".$this->comtypeid."\", \"compid\": \"".$this->compid."\", \"comname\": \""
            .$this->comname."\", \"comrename\": \"".$this->comrename."\",\"comdate\": \"".date("Y-m-d H:i:s", $this->comdate)."\", \"comment\": \"".$this->comment."\", \"comsort\": \""
            .$this->comsort."\", \"comvalid\": \"".$this->comvalid)." }";
        return $str;
    }

    public static function Exists($name){
        if(!isset($name)) return true;
        $str = "select count(*) from Comments where comid = :comid;";
        $paras = array(":comid" => $name);
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($com){
        if(!is_a($com, "Document")) return false;
        $str = "insert into Comments (comtype, comtypeid, compid, comname, comrename, comment, comsort, comvalid) values "
            ."(:comtype, :comtypeid, :compid, :comname, :comrename, :comment, :comsort, :comvalid); ";
        $paras = array(
            ":comtype" => $com->comtype, ":comtypeid" => $com->comtypeid, ":compid" => $com->compid, ":comname" => $com->comname, 
            ":comrename" => $com->comrename, ":comment" => $com->comment, ":comsort" => $com->comsort, ":comvalid" => $com->comvalid, 
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function GetComms($comtype = "other", $comtypeid = -1, $pid = -1, $valid = -1, $pagenum = 1, $pagesize = 0, $order = "sort"){
        $comtype = is_string($comtype) ? $comtype : "main";
        $comtypeid = is_int($comtypeid) ? $comtypeid : -1;
        $pid = is_int($pid) ? $pid : -1;
        $valid = is_int($valid) ? $valid : -1;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $count = "select count(*) as count ";
        $select = "select comid, comtype, comtypeid, compid, comname, comdate, comment, comsort, comvalid ";
        $where = "from Comments where 1 = 1 ";
        $paras = array();
        if($comtype != "other"){
            $where .= "and comtype = :comtype ";
            $paras[":comtype"] = $comtype;
        }
        if($comtypeid < -1) $where .= "and comtypeid > 0 ";
        else if($comtypeid > -1){
            $where .= "and comtypeid = :comtypeid ";
            $paras[":comtypeid"] = $comtypeid
        }
        if($pid < -1) $where .= "and compid > 0";
        else if($pid > -1){
            $where .= "and compid = :compid";
            $paras[":compid"] = $pid
        }
        if($valid < -1) $where .= "and comvalid > 0 ";
        else if($valid > -1){
            $where .= "and comvalid = :comvalid ";
            $paras[":comvalid"] = $valid;
        }
        $count .= $where."; ";
        $where .= "order by comid desc ";
        $select .= $where;
        if($pagenum > 0 && $pagesize > 0)
            $select .= "limit ".($pagesize > 1 ? ($pagenum - 1) * $pagesize : 0).", ".$pagesize."; ";
        else $select .= "; ";
        $en = new Entity();
        $list = new Resaults();
        $res = $en->Query($count, $paras);
        if($res) $list->page->MakePage((int)$res[0]["count"], $pagenum, $pagesize);
        $res = $en->Query($select, $paras);
        if($res) foreach($res as $key => $value) $list->list[] = new Comment($value);
        return $list;
    }

    public static function GetComm($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select comid, comtype, comtypeid, compid, comname, comdate, comment, comsort, comvalid "
            ."from Comments where comid = :comid; ";
        $paras = array(":comid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
        return new Comments($res);
    }

    public static function Valid($id, $valid = -1){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $valid = is_int($valid) ? $id : -1;
        $str = "update Comments set ";
        $paras = array();
        if($valid == -1){
            $str .= "comvalid = case when comvalid = 0 then 1 else 1 end where comid = :comid; ";
            $paras = array(":comid" => $id);
        }
        else {
            $str .= "comvalid = :comvalid where comid = :comid; ";
            $paras = array(":comid" => $id, ":comvalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras);
    }
}
?>