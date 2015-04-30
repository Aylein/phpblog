<?php
include_once("Entity.php");
include_once("Main.php");
include_once("Sign.php");
class SignOn{
    var $soid; // int primary key auto_increment,
    var $signid; // int not null,
    var $userid; // int not null,
    var $sotype; // nvarcahr(20) not null,
    var $sotypeid; // int default 0,
    var $socreatetime; // timestamp default current_timestamp,
    var $sosort; // int default 0,
    var $sovalid; // int default 1

    var $sign;

    public function __construct($array = null, $bo = false){
        if(!$array || !is_array($array)){
            $this->soid = 0;
            $this->signid = 0;
            $this->userid = 0;
            $this->sotype = "";
            $this->sotypeid = 0;
            $this->socreatetime = -1;
            $this->sosort = 0;
            $this->sovalid = 0;
        }
        else{
            $this->soid = isset($array["soid"]) && is_numeric($array["soid"]) ? (int)$array["soid"] : 0;
            $this->signid = isset($array["signid"]) && is_numeric($array["signid"]) ? (int)$array["signid"] : 0;
            $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
            $this->sotype = isset($array["sotype"]) ? $array["sotype"] : "";
            $this->sotypeid = isset($array["sotypeid"]) && is_numeric($array["sotypeid"]) ? (int)$array["sotypeid"] : 0;
            $this->socreatetime = isset($array["socreatetime"])  ? strtotime($array["socreatetime"]) : -1;
            $this->sosort = isset($array["sosort"]) && is_numeric($array["sosort"]) ? (int)$array["sosort"] : 0;
            $this->sovalid = isset($array["sovalid"]) && is_numeric($array["sovalid"]) ? (int)$array["sovalid"] : 0;
        }
        $this->sign = $bo && $this->signid > 0 ? Sign::Get($this->signid) : null;
    } 

    public static function Exists($signid, $type, $typeid, $execpt = null){
        if(!$name) return true;
        $str = "select count(*) from SignOn where signid = :signid and sotype = :sotype and sotypeid = :sotypeid ";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and soid != :soid";
            $paras[":soid"] = $execpt;
        }
        $paras[":signid"] = $signid;
        $paras[":sotype"] = $type;
        $paras[":sotypeid"] = $typeid;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($on, $deep = false){
        if(!$on instanceof SignOn) return new Message("对象类型不正确");
        if(SignOn::Exists($on->signid, $on->sotype, $on->sotypeid)) return new Message("要添加的类型名称已存在");
        $str = "insert into SignOn (signid, userid, sotype, sotypeid, sosort, sovalid) "
            ."values (:signid, :userid, :sotype, :sotypeid, :sosort, :sovalid); ";
        $str .= "select soid, signid, userid, sotype, sotypeid, socreatetime, sosort, sovalid "
            ."from SignOn where soid = @@identity; ";
        $paras = array(":signid" => $sign->signid, ":userid" => $sign->userid, ":sotype" => $sign->sotype, 
            ":sotypeid" => $sign->sotypeid, ":sosort" => $sign->sosort, ":sovalid" => $sign->sovalid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new SignOn($en[1][0], $deep)) : new Message("添加失败");
    }

    public static function Update($on, $deep = false){
        if(!$on instanceof SignOn) return new Message("对象类型不正确");
        if(SignOn::Exists($on->signid, $on->sotype, $on->sotypeid, $on->signid)) return new Message("要添加的类型名称已存在");
        $str = "update SignOn set signid = :signid, userid = :userid, sotype = :sotype, sotypeid = :sotypeid, "
            ."sosort = :sosort, sovalid = :sovalid where soid = :soid; ";
        $str .= "select soid, signid, userid, sotype, sotypeid, socreatetime, sosort, sovalid "
            ."from SignOn where soid = :soid; ";
        $paras = array(":signid" => $sign->signid, ":userid" => $sign->userid, ":sotype" => $sign->sotype, 
            ":sotypeid" => $sign->sotypeid, ":sosort" => $sign->sosort, ":sovalid" => $sign->sovalid, ":soid" => $sign->soid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new SignOn($en[1][0], $deep)) : new Message("修改失败");
        /*
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true, $on) : new Message("修改失败");
        */
    }

    public static function Add_Update($on, $deep = false){
        if(!$on instanceof SignOn) return new Message("对象类型不正确");
        return $on->soid > 0 ? SignOn::Update($on, $deep) : SignOn::Add($on, $deep);
    }

    public static function Count($search = null){
        $search = is_object($search) ? $search : new stdClass();
        $search->signid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->userid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->type = isset($search->name) ? strval($search->typepid) : "";
        $search->typeid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $count = "select count(*) as count ";
        $where = "from SignOn where 1 = 1 ";
        $paras = array();
        if($search->signid > 0){
            $where .= "and signid = :signid ";
            $paras[":signid"] = $search->signid;
        }
        if($search->userid > 0){
            $where .= "and userid = :userid ";
            $paras[":userid"] = $search->userid;
        }
        if($search->type != ""){
            $where .= "and sotype = :sotype ";
            $paras[":sotype"] = $search->type;
        }
        if($search->typeid > 0){
            $where .= "and sotypeid = :sotypeid ";
            $paras[":sotypeid"] = $search->typeid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and sovalid = :sovalid ";
            $paras[":sovalid"] = $search->valid;
        }
        $count .= $where.";";
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 1 || count($res[0]) != 1) return 0;
        return (int)$res[0][0]["count"];
    }

    public static function GetAll($search = null, $deep = false){
        $search = is_object($search) ? $search : new stdClass();
        $search->signid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->userid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->type = isset($search->name) ? strval($search->typepid) : "";
        $search->typeid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $search->order = isset($search->order) ? strval($search->order) : "";
        $count = "select count(*) as count ";
        $select = "select soid, signid, userid, sotype, sotypeid, socreatetime, sosort, sovalid ";
        $where = "from SignOn where 1 = 1 ";
        $paras = array();
        if($search->signid > 0){
            $where .= "and signid = :signid ";
            $paras[":signid"] = $search->signid;
        }
        if($search->userid > 0){
            $where .= "and userid = :userid ";
            $paras[":userid"] = $search->userid;
        }
        if($search->type != ""){
            $where .= "and sotype = :sotype ";
            $paras[":sotype"] = $search->type;
        }
        if($search->typeid > 0){
            $where .= "and sotypeid = :sotypeid ";
            $paras[":sotypeid"] = $search->typeid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and sovalid = :sovalid ";
            $paras[":sovalid"] = $search->valid;
        }
        $count .= $where."; ";
        $where .= "order by sosort desc, soid desc ";
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
        foreach($res[1] as $key => $value) $list[] = new SignOn($value, $deep);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id, $deep = false){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str .= "select soid, signid, userid, sotype, sotypeid, socreatetime, sosort, sovalid "
            ."from SignOn where soid = :soid; ";
        $paras = array(":soid" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new SignOn($res, $deep);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update SignOn set ";
        $paras = array();
        if(!$valid){
            $str .= "sovalid = case when sovalid = 0 then 1 else 1 end where soid = :soid; ";
            $paras = array(":soid" => $id);
        }
        else {
            $str .= "sovalid = :sovalid where soid = :soid; ";
            $paras = array(":soid" => $id, ":sovalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }
}
?>