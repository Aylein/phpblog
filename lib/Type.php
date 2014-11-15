<?php
include("Entity.php");
//类型
class Type{
    //标识
    var $typeid; // int primary key auto_increment,
    //自关联标识
    var $typepid; // int default 0,
    //类型名
    var $typename; // nvarchar(15) not null,
    //排序
    var $typesort; // int default 0,
    //是否可用
    var $typevalid; // int default 1

    //构造函数
    public function __construct($array = null){
        //默认构造函数
        if(!$array || !is_array($array)) {
            $this->typeid = 0;
            $this->typepid = 0;
            $this->typename = "";
            $this->typesort = 0;
            $this->typevalid = 0;
        }
        //构造函数
        else {
            $this->typeid = isset($array["typeid"]) && is_int($array["typeid"]) ? (int)$array["typeid"] : 0;
            $this->typepid = isset($array["typepid"]) && is_int($array["typepid"]) ? (int)$array["typepid"] : 0;
            $this->typename = isset($array["typename"])  ? $array["typename"] : "";
            $this->typesort = isset($array["typesort"]) && is_int($array["typesort"]) ? (int)$array["typesort"] : 0;
            $this->typevalid = isset($array["typevalid"]) && is_int($array["typevalid"]) ? (int)$array["typevalid"] : 0;
        }
    }

    //Json格式
    public function MakeJson(){
        $str = "{ \"typeid\": \"".$this->typeid."\", \"typepid\": \"".$this->typepid."\", \"typename\": \""
            .$this->typename."\", \"typesort\": \"".$this->typesort."\", \"typevalid\": \"".$this->typevalid."\" }";
        return $str;
    }

    //检查存在
    public static function Exists($name){
        if(!$name) return ture;
        $str;
        $paras;
        if(is_int($name)) { 
            $str = "select count(*) from Type where typeid = :typeid; ";
            $paras = array(":typeid" => $name);
        }
        else {
            $str = "select count(*) from Type where typename = :typename; ";
            $paras = array(":typename" => $name);
        }
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($type){
        if (!is_a($type, "Type")) return false;
        $type->typesort = $type->typesort ? $type->typesort : 0;
        $type->typevalid = $type->typevalid ? $type->typevalid : 1;
        $str = "insert into Type (typename, typesort, typevalid) values (:typename, :typesort, :typevalid);";
        $paras = array(":typename" => $type->typename, ":typesort" => $type->typesort, ":typevalid" => $type->typevalid);
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    public static function GetTypes($typeid = 0, $pagenum = 1, $pagesize = 0){
        $typeid = is_int($typeid) ? $typeid : 0;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $str = "select typeid, typepid, typename, typesort, typevalid from Types ";
        $paras = array();
        if($typeid > 0) { 
            $str .= "where typepid = :typepid ";
            $paras[":typeid"] = $typeid;
        }
        if($pagenum > 0) {
            $str .= "limit :offset, :rows; ";
            $paras[":offset"] = $pagesize > 1 ? ($pagenum - 1) * $pagesize : 0;
            $paras[":rows"] = $pagesize;
        }
        $str .= ";";
        print_r($paras);
        die();
        $en = new Entity();
        $res = $en->Query($str, $paras);
        if(!$res) return false;
        $list = array();
        for($i = 0, $z = count($res); $i < $z; $i++) $list[] = new Type($res[$i]);
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