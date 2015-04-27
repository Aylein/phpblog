<?php
include_once("Entity.php");
include_once("Stage.php");
class Document{
    var $docid; // int primary key auto_increment,
    var $stgid; // int references Stage(stgid) on delete cascade on update cascade,
    var $doccontent; // nvarchar(12000) not null,
    var $docsort; // int default 0,
    var $docvalid; // int default 1

    var $stage;

    public function __construct($array = null, $bo = false){
        if($array == null || !is_array($array)){
            $this->docid = 0;
            $this->stgid = 0;
            $this->doccontent = "";
            $this->docsort = 0;
            $this->docvalid = 0;
        }
        else{
            $this->docid = isset($array["docid"]) && is_numeric($array["docid"]) ? (int)$array["docid"] : 0;
            $this->stgid = isset($array["stgid"]) && is_numeric($array["stgid"]) ? (int)$array["stgid"] : 0;
            $this->doccontent = isset($array["doccontent"]) ? $array["doccontent"] : "";
            $this->docsort = isset($array["docsort"]) && is_numeric($array["docsort"]) ? (int)$array["docsort"] : 0;
            $this->docvalid = isset($array["docvalid"]) && is_numeric($array["docvalid"]) ? (int)$array["docvalid"] : 0;
        }
        $this->stage = $bo && $this->stgid > 0 ? Stage::Get($this->stgid) : null;
    }

    ///*
    public static function Exists($stgid, $execpt = null){
        if(!$title) return true;
        $str = "select count(*) from Documents where stgid = :stgid";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and docid != :docid";
            $paras[":docid"] = $execpt;
        }
        $paras[":stgid"] = $stgid;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }
    //*/

    public static function Add($doc){
        if(!$doc instanceof Documents) return new Message("对象类型不正确");
        //if(Documents::Exists($doc->doccontent)) return new Message("要添加的文章名称已存在");
        $str = "insert into Documents (stgid, doccontent, :docsort, :docvalid); ";
        $str .= "select docid, stgid, doccontent, docsort, docvalid from Documents where docid = @@identity; ";
        $paras = array(
            ":stgid" => $doc->stgid, ":doccontent" => $doc->doccontent, ":docsort" => $doc->docsort, 
            ":docvalid" => $doc->docvalid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("创建成功", true, new Document($obj[1][0])) : new Message("创建失败");
    }

    public static function Update($doc){
        if(!$doc instanceof Documents) return new Message("对象类型不正确");
        //if(Documents::Exists($doc->doccontent, $doc->docid)) return new Message("要添加的文章名称已存在");
        $str = "update Documents set stgid = :stgid, doccontent = :doccontent, docsort = :docsort, "
            ."docvalid = :docvalid where docid = :docid ";
        $str .= "select docid, stgid, doccontent, docsort, docvalid from Documents where docid = :docid; ";
        $paras = array(
            ":stgid" => $doc->stgid, ":doccontent" => $doc->doccontent, ":docsort" => $doc->docsort, 
            ":docvalid" => $doc->docvalid, ":docid" => $doc->docid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new Documents($en[1][0], $deep)) : new Message("修改失败");
    }

    public static function Add_Update($doc){
        if(!$doc instanceof Documents) return new Message("对象类型不正确");
        return $doc->docid > 0 ? Documents::Update($doc) : Documents::Add($doc);
    }

    public static function GetAll($search = null, $deep = false){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->stgid = isset($search->stgid) && is_numeric($search->stgid) ? (int)$search->stgid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $count = "select count(*) as count ";
        $select = "select docid, stgid, doccontent, docsort, docvalid ";
        $where = "from Documents where 1 = 1 ";
        $paras = array();
        if($search->stgid > 0){ 
            $where .= "and stgid = :stgid ";
            $paras[":stgid"] = ($search->stgid;
        }
        if($search->valid > 0){ 
            $where .= "and docvalid = :valid ";
            $paras[":valid"] = ($search->valid;
        }
        $count .= $where."; ";
        $where .= "order by docsort desc, docid desc ";
        $select .= $where;
        else $select .= "; ";
        $count .= $select;
        $list = array();
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 2 || count($res[0]) != 1) return new Resaults();
        foreach($res[1] as $key => $value) $list[] = new Documents($value, $deep);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id, $deep = false){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select docid, stgid, doccontent, docsort, docvalid from Documents where docid = :docid; ";
        $paras = array(":docid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return null;
        return new Document($res);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Documents set ";
        $paras = array();
        if(!$valid){
            $str .= "docvalid = case when docvalid = 0 then 1 else 1 end where docid = :docid; ";
            $paras = array(":docid" => $id);
        }
        else {
            $str .= "docvalid = :docvalid where docid = :docid; ";
            $paras = array(":docid" => $id, ":docvalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }
}
?>