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

    public function MakeJson(){
        $str = "{ \"id\": \"".$this->id."\", \"_key\": \"".$this->_key."\", \"_value\": \"".$this->_value."\" }";
        return $str;
    }

    public static function Exists($name){
        if(!isset($name)) return true;
        $str = "select count(*) from Main where ";
        $paras = array();
        if(is_int($name)){
            $str .= "id = :id; ";
            $paras[":id"] = $name;
        }
        else{
            $str .= "_key = :_key; ";
            $paras[":_key"] = $name;
        }
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function GetValue($key){
        if(!isset($key) || is_string($key)) return false;
        $str = "select _value from Main where _key = $_key; ";
        $array = array(":_key" => $key);
        return (new Entity())->Scalar($str, $paras);
    }

    public static function Add($main){
        if(is_a($main, "Main")) return false;
        $str = "insert into Main (_key, _value) values (:_key, :_value); ";
        $paras = array(":_key" => $main->_key, ":_value" => $main->_value);
        return (new Entity())->Exec($str, $paras);
    }

    public static function Update($main){
        if(is_a($main, "Main")) return false;
        $str = "update Main set _key = :_key, _value = :_value where id = :id; ";
        $paras = array(":_key" => $main->_key, ":_value" => $main->_value, ":id" => $main->id);
        return (new Entity())->Exec($str, $paras);
    }

    public static function GetMains($pagenum = 1, $pagesize = 0){
        $str = "select id, _key, _value ";
        $count = "select count(*) as count ";
        $where = "from Main where 1 = 1 ";
        $paras = array();
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
        if($res) foreach($res as $key => $value) $list->list[] = new Main($value);
        return $list;
    }

    public static function GetMain($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select id, _key, _value from Main where id = :id; ";
        $paras = array(":id" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
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
        if($count == null || $page == null || $rows == null || $page < 1 || $rows < 1) {
            $this->totalnum = 0;
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
        $this->list = array();
        $this->page = new Page();
    }
}

class Message{
    var $res;
    var $code;
    var $msg;
    var $obj;

    public function __construct($res = false, $code = "", $msg = "", $obj = null){
        $this->res = $res;
        $this->code = $code;
        $this->msg = $msg;
        $this->obj = $obj;
    }
}
?>