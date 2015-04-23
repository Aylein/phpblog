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
    var $typecreatetime;
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
            $this->typecreatetime = isset($array["typecreatetime"])  ? strtotime($array["typecreatetime"]) : -1;
            $this->typesort = isset($array["typesort"]) && is_numeric($array["typesort"]) ? (int)$array["typesort"] : 0;
            $this->typevalid = isset($array["typevalid"]) && is_numeric($array["typevalid"]) ? (int)$array["typevalid"] : 0;
        }
    }

    //检查存在
    public static function Exists($name, $execpt = null){
        if(!$name) return true;
        $str = "select count(*) from Types where typename = :typename";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and typeid != :typeid";
            $paras[":typeid"] = $execpt;
        }
        $paras[":typename"] = $name;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($type){
        if(!$type instanceof Type) return new Message("对象类型不正确");
        if(Type::Exists($type->typename)) return new Message("要添加的类型名称已存在");
        $str = "insert into Types (typepid, typeshow, typename, typesort, typevalid) "
            ."values (:typepid, :typeshow, :typename, :typesort, :typevalid); ";
        $str .= "select typeid, typepid, typeshow, typename, typecreatetime, typesort, typevalid "
            ."from Types where typeid = @@identity; ";
        $paras = array(":typepid" => $type->typepid, ":typeshow" => $type->typeshow,
            ":typename" => $type->typename, ":typesort" => $type->typesort, ":typevalid" => $type->typevalid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Type($en[1][0])) : new Message("添加失败");
    }

    public static function Update($type){
        if(!$type instanceof Type) return new Message("对象类型不正确");
        if(Type::Exists($type->typename, $type->typeid)) return new Message("要添加的类型名称已存在");
        $str = "update Types set typepid = :typepid, typeshow = :typeshow, typename = :typename, typesort = :typesort, "
            ."typevalid = :typevalid where typeid = :typeid; ";
        $str .= "select typeid, typepid, typename, typecreatetime, typesort, typevalid from Types where typeid = :typeid; ";
        $paras = array(":typepid" => $type->typepid, ":typeshow" => $type->typeshow, ":typename" => $type->typename,
            ":typesort" => $type->typesort, ":typevalid" => $type->typevalid, ":typeid" => $type->typeid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new Type($en[1][0], $deep)) : new Message("修改失败");
    }

    public static function Add_Update($type){
        if(!$type instanceof Type) return new Message("对象类型不正确");
        return $type->typeid > 0 ? Type::Update($type) : Type::Add($type);
    }

    public static function GetAll($search = null){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->typepid = isset($search->typepid) && is_numeric($search->typepid) ? (int)$search->typepid : -2;
        $search->show = isset($search->show) && is_numeric($search->show) ? (int)$search->show : -1;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $count = "select count(*) as count ";
        $select = "select typeid, typepid, typeshow, typename, typecreatetime, typesort, typevalid ";
        $where = "from Types where 1 = 1 ";
        $paras = array();
        if($search->typepid == -1) $where .= "and typepid > 0 ";
        else if($search->typepid > -1){
            $where .= "and typepid = :typepid ";
            $paras[":typepid"] = $search->typepid;
        }
        if($search->show == 1 || $search->show == 0){
            $where .= "and typeshow = :typeshow ";
            $paras[":typeshow"] = $search->show;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and typevalid = :typevalid ";
            $paras[":typevalid"] = $search->valid;
        }
        $count .= $where.";";
        $where .= "order by typesort desc, typeid desc ";
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
        foreach($res[1] as $key => $value) $list[] = new Type($value);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select typeid, typepid, typename, typecreatetime, typesort, typevalid from Types where typeid = :typeid; ";
        $paras = array(":typeid" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new Type($res);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Types set ";
        $paras = array();
        if(!$valid){
            $str .= "typevalid = case when typevalid = 0 then 1 else 1 end where typeid = :typeid; ";
            $paras = array(":typeid" => $id);
        }
        else {
            $str .= "typevalid = :typevalid where typeid = :typeid; ";
            $paras = array(":typeid" => $id, ":typevalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }
}
?>