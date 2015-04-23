<?php
include_once("Entity.php");
class Commen{
    public static function GetIP(){
        if(!empty($_SERVER["HTTP_CLIENT_IP"])) return $_SERVER["HTTP_CLIENT_IP"];
        elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
        elseif(!empty($_SERVER["REMOTE_ADDR"])) return $_SERVER["REMOTE_ADDR"];
        else return "noipaddress";
    }
    public static function UUID() {
        if(function_exists('com_create_guid')) return com_create_guid();
        else {
            //mt_srand((double)microtime() * 10000); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
            $charid = strtoupper(md5(uniqid(rand(), true))); //根据当前时间（微秒计）生成唯一id.
            $hyphen = chr(45); // "-"
            return substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid, 12, 4).
                $hyphen.substr($charid, 16, 4).$hyphen.substr($charid, 20, 12);
        }
    }
    public static function Rand($len){
        $arr = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n",
            "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z","A", "B", "C", "D",
            "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
            "U", "V", "W", "X", "Y", "Z", "_", "1", "2", "3", "4", "5", "6", "7", "8", "9", 
            "0"];
        $name = "";
        for($i = 0; $i < $len; $i++) $name .= $arr[rand(0, 62)];
        return $name;
    }
}

class Main{
    var $id;
    var $_key;
    var $_value;

    public function __construct($array = null){
        if(!isset($array) || !is_array($array)){
            $this->id = 0;
            $this->_key = "";
            $this->_value = "";
        }
        else{
            $this->id = isset($array["id"]) && is_numeric($array["id"]) ? (int)$array["id"] : 0;
            $this->_key = isset($array["_key"]) ? $array["_key"] : "";
            $this->_value = isset($array["_value"]) ? $array["_value"] : "";
        }
    }

    //检查存在
    public static function Exists($key, $execpt = null){
        if(!$key) return true;
        $str = "select count(*) from Main where _key = :key";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and id != :id";
            $paras[":id"] = $execpt;
        }
        $paras[":key"] = $key;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function GetValue($key){
        if(!isset($key) || is_string($key)) return false;
        $str = "select _value from Main where _key = :_key; ";
        $array = array(":_key" => $key);
        return (new Entity())->Scalar($str, $paras);
    }

    public static function Add($main){
        if(!$main instanceof Main) return new Message("对象类型不正确");
        if(Main::Exists($main->_key)) return new Message("要添加的key值已存在");
        $str = "insert into Main (_key, _value) values (:_key, :_value); ";
        $str = "select id, _key, _value from Main where id = @@identity; ";
        $paras = array(":_key" => $main->_key, ":_value" => $main->_value);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Main($en[1][0])) : new Message("添加失败");
    }

    public static function Update($main){
        if(!$main instanceof Main) return new Message("对象类型不正确");
        if(Main::Exists($main->_key, $main->id)) return new Message("要添加的key值已存在");
        $str = "update Main set _key = :_key, _value = :_value where id = :id; ";
        $str .= "select id, _key, _value from Main where id = :id; ";
        $paras = array(":_key" => $main->_key, ":_value" => $main->_value, ":id" => $main->id);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new Main($en[1][0], $deep)) : new Message("修改失败");
    }

    public static function Add_Update($main){
        if(!$main instanceof Main) return new Message("对象类型不正确");
        return $main->id > 0 ? Main::Update($main) : Main::Add($main);
    }

    public static function GetAll($search = null){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $str = "select id, _key, _value ";
        $count = "select count(*) as count ";
        $where = "from Main ";
        $paras = array();
        $count .= $where.";";
        $where .= "order by typesort desc, typeid desc ";
        $select .= $where;
        if($search->page > 0 && $search->rows > 0){            
            $select .= "limit :page, :rows; ";
            $paras[":page"] = ($search->page - 1) * $search->rows;
            $paras[":rows"] = $search->rows;
        }
        else $select .= "; ";
        $list = array();
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 2 || count($res[0]) != 1) return new Resaults();
        foreach($res[1] as $key => $value) $list[] = new Main($value);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select id, _key, _value from Main where id = :id; ";
        $paras = array(":id" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new Main($res);
    }
}

class Page{
    var $page;
    var $rows;
    var $totalnum;
    var $totalpage;

    public function __construct($count = null, $page = null, $rows = null){
        $this->MakePage($count, $page, $rows);
    }

    public function MakePage($count, $page, $rows){
        $count = $count != null && is_numeric($count) ? (int)$count : 0;
        if($page == null || $rows == null || $page < 1 || $rows < 1) {
            $this->totalnum = $count;
            $this->page = 1;
            $this->rows = 0;
            $this->totalpage = 0;
        }
        else {
            $this->totalnum = $count;
            $this->page = $page;
            $this->rows = $rows;
            $this->totalpage = ceil($count / $size);
        }
    }
}

class Resaults{
    var $list;
    var $page;

    public function __construct(){
        $args = func_get_args();
        $len = func_num_args();
        if($len < 1){
            $this->list = array();
            $this->page = new Page();
        }
        else if($len == 1){
            $this->list = is_array($args[0]) ? $args[0] : array();
            $this->page = new Page();
        }
        else if($len == 2){
            $this->list = is_array($args[0]) ? $args[0] : array();
            $this->page = $args[1] instanceof Page ? $args[1] : new Page();
        }
        else if($len == 4){
            $this->list = is_array($args[0]) ? $args[0] : array();
            $this->page = is_numeric($args[1]) && is_numeric($args[2]) && is_numeric($args[3]) ? 
                new Page($args[1], $args[2], $args[3]) : new Page();
        }
    }
}

class Message{
    var $res;
    var $code;
    var $msg;
    var $obj;

    public function __construct($msg = "", $res = false, $obj = null, $code = ""){
        $this->res = $res;
        $this->code = $code;
        $this->msg = $msg;
        $this->obj = $obj;
    }
}
?>