<?php
include_once("Entity.php");
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
    var $pagenum;
    var $pagesize;
    var $totalnum;
    var $totalpage;

    public function __construct(){
        $this->totalnum = 0;
        $this->pagenum = 1;
        $this->pagesize = 0;
        $this->totalpage = 0;
    }

    public function MakePage($count, $page, $size){
        $size = is_int($size) ? $size : 0;
        if($size <= 0) return false;
        $count = is_int($count) ? $count : 0;
        $page = is_int($page) ? $page : 0;
        $this->totalnum = $count;
        $this->pagenum = $page;
        $this->pagesize = $size;
        $this->totalpage = (int)($count / $size);
        $this->totalpage = $this->totalpage < $count / $size ? $this->totalpage + 1 : $this->totalpage;
    }

    public function MakeJson(){
        $str = "{ \"pagenum\": \"".$this->pagenum."\", \"pagesize\": \"".$this->pagesize."\", \"totalnum\": \""
            .$this->totalnum."\", \"totalpage\": \"".$this->totalpage."\" }";
        return $str;
    }
}

class Resaults{
    var $list;
    var $page;

    public function __construct(){
        $this->list = array();
        $this->page = new Page();
    }

    public function MakeJson(){
        $str = "{ \"page\": ".$this->page->MakeJson().", \"list\": [ ";
        $count = count($this->list);
        if($count > 0) foreach($this->list as $key => $value) $str .= $value->MakeJson().", ";
        if($count > 0) $str = substr($str, 0, -2)." ";
        $str .= " ] }";
        return $str;
    }
}
?>