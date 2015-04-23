<?php
include_once("Entity.php");
include_once("Main.php");
include_once("User.php");
class Sign{
    var $signid; // int primary key auto_increment,
    var $signname; // nvarchar(20) not null,
    var $signcreatetime; // timestamp default current_timestamp,
    var $userid; // int not null,
    var $signsort; // int default 0,
    var $signvalid; // int default 1

    var $user;

    public function __construct($array = null, $bo = false){
        if(!$array || !is_array($array)){
            $this->signid = 0;
            $this->signname = "";
            $this->signcreatetime = -1;
            $this->userid = 0;
            $this->signsort = 0;
            $this->signvalid = 0;
        }
        else{
            $this->signid = isset($array["signid"]) && is_numeric($array["signid"]) ? (int)$array["signid"] : 0;
            $this->signname = isset($array["signname"]) ? $array["signname"] : "";
            $this->signcreatetime = isset($array["signcreatetime"])  ? strtotime($array["signcreatetime"]) : -1;
            $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
            $this->signsort = isset($array["signsort"]) && is_numeric($array["signsort"]) ? (int)$array["signsort"] : 0;
            $this->signvalid = isset($array["signvalid"]) && is_numeric($array["signvalid"]) ? (int)$array["signvalid"] : 0;
        }
        $this->user = $bo && $this->userid > 0 ? User::Get($this->userid) : null;
    }

    public static function Exists($name, $execpt = null){
        if(!$name) return true;
        $str = "select count(*) from Signs where signname = :signname";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and signid != :signid";
            $paras[":signid"] = $execpt;
        }
        $paras[":signname"] = $name;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($sign){
        if(!$sign instanceof Sign) return new Message("对象类型不正确");
        if(Sign::Exists($sign->signname)) return new Message("要添加的类型名称已存在");
        $str = "insert into Signs (signname, userid, signsort, signvalid) "
            ."values (:signname, :userid, :signsort, :signvalid); ";
        $str .= "select signid, signname, signcreatetime, userid, signsort, signvalid "
            ."from Signs where signid = @@identity; ";
        $paras = array(":signname" => $sign->signname, ":userid" => $sign->userid,
            ":signsort" => $sign->signsort, ":signvalid" => $sign->signvalid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Sign($en[1][0])) : new Message("添加失败");
    }

    public static function Update($sign){
        if(!$sign instanceof Sign) return new Message("对象类型不正确");
        if(Sign::Exists($sign->signname, $sign->signid)) return new Message("要添加的类型名称已存在");
        $str = "update Signs set signname = ?signname, userid = ?userid, signsort = ?signsort, signvalid = ?signvalid "
            ."where signid = :signid; ";
        $str .= "select signid, signname, signcreatetime, userid, signsort, signvalid "
            ."from Signs where signid = :signid; ";
        $paras = array(":signname" => $sign->signname, ":userid" => $sign->userid,
            ":signsort" => $sign->signsort, ":signvalid" => $sign->signvalid, ":signid" => $sign->signid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new Sign($en[1][0], $deep)) : new Message("修改失败");
    }

    public static function Add_Update($sign){
        if(!$sign instanceof Sign) return new Message("对象类型不正确");
        return $sign->signid > 0 ? Sign::Update($sign) : Sign::Add($sign);
    }

    public static function GetAll($search = null, $deep = false){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->name = isset($search->name) ? strval($search->typepid) : "";
        $search->userid = isset($search->userid) && is_numeric($search->userid) ? (int)$search->userid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $search->order = isset($search->order) ? strval($search->order) : "";
        $count = "select count(*) as count ";
        $select = "select signid, signname, signcreatetime, userid, signsort, signvalid ";
        $where = "from Signs where 1 = 1 ";
        $paras = array();
        if($search->name != ""){
            $where .= "and ( ";
            $arr = explode(" ", $search->name);
            for($i = 0, $z = count($arr); $i < $z; $i++)
            {
                $where .= "signname like concat(\"%\", :str_a_".$i.", \"%\") ";
                if ($i < $z - 1) $where .="or ";
                $paras[":str_a_".$i] = $arr[$i];
            }
            $where .= ") ";
        }
        if($search->userid > 0){
            $where .= "and userid = :userid ";
            $paras[":userid"] = $search->userid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and signvalid = :signvalid ";
            $paras[":signvalid"] = $search->valid;
        }
        $count .= $where.";";
        switch($search->$order){
            case "num":
                $where .= "order by (select count(*) from SignOn where signid = signid) desc, signsort desc, signid desc ";
                break;
            default: 
                $where .= "order by signsort desc, signid desc ";
                break;
        }
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
        foreach($res[1] as $key => $value) $list[] = new Sign($value, $deep);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id, $deep = false){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select signid, signname, signcreatetime, userid, signsort, signvalid "
            ."from Signs where signid = :signid; ";
        $paras = array(":signid" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new Sign($res, $deep);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Signs set ";
        $paras = array();
        if(!$valid){
            $str .= "signvalid = case when signvalid = 0 then 1 else 1 end where signid = :signid; ";
            $paras = array(":signid" => $id);
        }
        else {
            $str .= "signvalid = :signvalid where signid = :signid; ";
            $paras = array(":signid" => $id, ":signvalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }
}
?>