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

    //检查存在
    public static function Exists($name){
        if(!$name) return ture;
        $str = "select count(*) from Types where ";
        $paras = array();
        if(is_int($name)){
            $str .= "typeid = :typeid; ";
            $paras[":typeid"] = $name;
        }
        else{
            $str = "typename = :typename; ";
            $paras[":typename"] = $name;
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
        //if(!Type::exists($type->typeid)) return Type::Add($type);
        $str = "update Types set typepid = :typepid, typeshow = :typeshow, typename = :typename, typesort = :typesort, "
            ."typevalid = :typevalid where typeid = :typeid; ";
        $paras = array(":typepid" => $type->typepid, ":typeshow" => $type->typeshow, ":typename" => $type->typename, 
            ":typesort" => $type->typesort, ":typevalid" => $type->typevalid, ":typeid" => $type->typeid);
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    public static function GetTypes($typepid = -1, $show = -1, $valid = -1, $pagenum = 1, $pagesize = 0){
        $typepid = is_int($typepid) ? $typepid : 0;
        $show = is_int($show) ? $show : -1;
        $valid = is_int($valid) ? $valid : -1;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $count = "select count(*) as count ";
        $select = "select typeid, typepid, typeshow, typename, typesort, typevalid ";
        $where = "from Types where 1 = 1 ";
        $paras = array();
        if ($typepid < -1) $where .= "and typepid > 0 ";
        else if($typepid > -1){
            $where .= "and typepid = :typepid ";
            $paras[":typepid"] = $typepid;
        }
        if($show < -1) $where .= "and typeshow > 0 ";
        else if($show > -1){
            $where .= "and typeshow = :typeshow ";
            $paras[":typeshow"] = $show;
        }
        if($valid < -1) $where .= "and typevalid > 0 ";
        else if($valid > -1){
            $where .= "and typevalid = :typevalid ";
            $paras[":typevalid"] = $valid;
        }
        $count .= $where.";";
        $where .= "order by typesort desc, typeid desc ";
        $select .= $where;
        if($pagenum > 0 && $pagesize > 0)
            $select .= "limit ".($pagesize > 1 ? ($pagenum - 1) * $pagesize : 0).", ".$pagesize."; ";
        else $select .= "; ";
        $en = new Entity();
        $list = new Resaults();
        $res = $en->Query($count, $paras);
        if($res) $list->page->MakePage((int)$res[0]["count"], $pagenum, $pagesize);
        $res = $en->Query($select, $paras);
        if($res) foreach($res as $key => $value) $list->list[] = new Type($value);
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

    public static function Valid($id, $valid = -1){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $valid = is_int($valid) ? $id : -1;
        $str = "update Types set ";
        $paras = array();
        if($valid == -1){
            $str .= "typevalid = case when typevalid = 0 then 1 else 1 end where typeid = :typeid; ";
            $paras = array(":typeid" => $id);
        }
        else {
            $str .= "typevalid = :typevalid where typeid = :typeid; ";
            $paras = array(":typeid" => $id, ":typevalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras);
    }
}
?>