<?php
include_once("Entity.php");
include_once("Main.php");
//类型
class Type{
    //标识
    var $typeid; // int primary key auto_increment,
    //自关联标识
    var $typepid; // int default 0,
    //是否在顶部显示
    var $typeshow;
    //类型名
    var $typename; // nvarchar(15) not null,
    //排序
    var $typesort; // int default 0,
    //是否可用
    var $typevalid; // int default 1

    //构造函数
    public function __construct($array = null){
        //默认构造函数
        if(!$array || !is_array($array)){
            $this->typeid = 0;
            $this->typepid = 0;
            $this->typeshow = 0;
            $this->typename = "";
            $this->typesort = 0;
            $this->typevalid = 0;
        }
        //构造函数
        else{
            $this->typeid = isset($array["typeid"]) && is_numeric($array["typeid"]) ? (int)$array["typeid"] : 0;
            $this->typepid = isset($array["typepid"]) && is_numeric($array["typepid"]) ? (int)$array["typepid"] : 0;
            $this->typeshow = isset($array["typeshow"]) && is_numeric($array["typeshow"]) ? (int)$array["typeshow"] : 0;
            $this->typename = isset($array["typename"])  ? $array["typename"] : "";
            $this->typesort = isset($array["typesort"]) && is_numeric($array["typesort"]) ? (int)$array["typesort"] : 0;
            $this->typevalid = isset($array["typevalid"]) && is_numeric($array["typevalid"]) ? (int)$array["typevalid"] : 0;
        }
    }

    //Json格式
    public function MakeJson(){
        $str = "{ \"typeid\": \"".$this->typeid."\", \"typepid\": \"".$this->typepid."\", \"typeshow\": \"".$this->typeshow
            ."\", \"typename\": \"".$this->typename."\", \"typesort\": \"".$this->typesort."\", \"typevalid\": \"".$this->typevalid."\" }";
        return $str;
    }

    //检查存在
    public static function Exists($name){
        if(!$name) return ture;
        $str;
        $paras;
        if(is_int($name)){ 
            $str = "select count(*) from Types where typeid = :typeid; ";
            $paras = array(":typeid" => $name);
        }
        else{
            $str = "select count(*) from Types where typename = :typename; ";
            $paras = array(":typename" => $name);
        }
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($type){
        if(!is_a($type, "Type")) return false;
        $str = "insert into Types (typepid, typeshow, typename, typesort, typevalid) "
            ."values (:typepid, :typeshow, :typename, :typesort, :typevalid);";
        $paras = array(":typepid" => $type->typepid, ":typeshow" => $type->typeshow, 
            ":typename" => $type->typename, ":typesort" => $type->typesort, ":typevalid" => $type->typevalid);
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    public static function Update($type){
        if(!is_a($type, "Type")) return false;
        if(!Type::exists($type->typeid)) return Type::Add($type);
        $str = "update Types set typepid = :typepid, typeshow = :typepid, typename = :typepid, typesort = :typepid, "
            ."typevalid = :typepid where typeid = :typeid; ";
        $paras = array(":typepid" => $type->typepid, ":typeshow" => $type->typeshow, ":typename" => $type->typename, 
            ":typesort" => $type->typesort, ":typevalid" => $type->typevalid, ":typeid" => $type->typeid);
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    public static function GetTypes($typeid = 0, $show = -1, $pagenum = 1, $pagesize = 0){
        $typeid = is_int($typeid) ? $typeid : 0;
        $show = is_int($show) ? $show : -1;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $str = "select typeid, typepid, typeshow, typename, typesort, typevalid from Types ";
        $con = "select count(*) as count from Types ";
        $paras = array();
        if($typeid > 0){ 
            $str .= "where typepid = :typepid ";
            $con .= "where typepid = :typepid ";
            $paras[":typeid"] = $typeid;
        }
        if($show != -1){
            $str .= "where typeshow = :typeshow ";
            $con .= "where typeshow = :typeshow ";
            $paras[":typeshow"] = $show;
        }
        if($pagenum > 0){
            $str .= "limit :offset, :rows; ";
            $paras[":offset"] = $pagesize > 1 ? ($pagenum - 1) * $pagesize : 0;
            $paras[":rows"] = $pagesize;
        }
        $str .= ";";
        $con .= ";";
        $en = new Entity();
        $res = $en->Query($con.$str, $paras);
        $list = new Resaults();
        if(!$res || count($res) != 2) return $list;
        $list->page->MakePage((int)$res[0]["count"], $pagenum, $pagesize);
        for($i = 0, $z = count($res[1]); $i < $z; $i++) $list->list[] = new Type($res[1][$i]);
        return $list;
    }

    public static function GetType($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select typeid, typepid, typename, typesort, typevalid from Types where typeid = :typeid; ";
        $paras = array(":typeid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
        return new Type($res);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str;
        $paras;
        if($valid == null || !is_int($valid)){
            $str = "update Types set typevalid = case when typevalid = 0 then 1 else 1 end where typeid = :typeid; ";
            $paras = array(":typeid" => $id);
        }
        else {
            $str = "update Types set typevalid = :typevalid where typeid = :typeid; ";
            $paras = array(":typeid" => $id, ":typevalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras);
    }
}
?>