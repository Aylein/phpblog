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
        if(!$array || !is_array($array)) return;
        $this->typeid = isset($array["typeid"]) && is_numeric($array["typeid"]) ? (int)$array["typeid"] : 0;
        $this->typepid = isset($array["typepid"]) && is_numeric($array["typepid"]) ? (int)$array["typepid"] : 0;
        $this->typename = isset($array["typename"])  ? $array["typename"] : "";
        $this->typesort = isset($array["typesort"]) && is_numeric($array["typesort"]) ? (int)$array["typesort"] : 0;
        $this->typevalid = isset($array["typevalid"]) && is_numeric($array["typevalid"]) ? (int)$array["typevalid"] : 0;
    }

    public function MakeJson(){

    }

    public static function Add($type){
        if (!is_a($type, "Type")) return false;
        $type->typesort = $type->typesort ? $type->typesort : 0;
        $type->typevalid = $type->typevalid ? $type->typevalid : 0;
        $str = "insert into Type (typename, typesort, typevalid) values (:typename, :typesort, :typevalid);";
        $paras = array(":typename" => $type->typename, ":typesort" => $type->typesort, ":typevalid" => $type->typevalid);
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    
}
?>