<?php
include_once("Entity.php");
include_once("Type.php");
class Document{
    var $docid; // int primary key auto_increment,
    var $typeid; // int references Types(typeid) on delete cascade on update cascade,
    var $docpid; // int default 0,
    var $doctitle; // nvarchar(15) not null,
    var $docsubtitle; // nvarchar(50) default "",
    var $docstgnum; // int default 0,
    var $doccomnum; // int default 0,
    var $docview; // int default 0,
    var $doccreatetime; // timestamp default current_timestamp,
    var $docupdatetime; // timestamp default current_timestamp on update current_timestamp,
    var $docsort; // int default 0,
    var $docvalid; // int default 1

    var $Type;

    public function __construct($array = null, $bo = false){
        if($array == null || !is_array($array)){
            $this->docid = 0;
            $this->typeid = 0;
            $this->doctitle = "";
            $this->docsubtitle = "";
            $this->docstgnum = 0;
            $this->doccomnum = 0;
            $this->docview = 0;
            $this->doccreatetime = -1;
            $this->docupdatetime = -1;
            $this->docsort = 0;
            $this->docvalid = 0;
        }
        else{
            $this->docid = isset($array["docid"]) && is_numeric($array["docid"]) ? (int)$array["docid"] : 0;
            $this->typeid = isset($array["typeid"]) && is_numeric($array["typeid"]) ? (int)$array["typeid"] : 0;
            $this->doctitle = isset($array["doctitle"]) ? $array["doctitle"] : "";
            $this->docsubtitle = isset($array["docsubtitle"]) ? $array["docsubtitle"] : "";
            $this->docstgnum = isset($array["docstgnum"]) && is_numeric($array["docstgnum"]) ? (int)$array["docstgnum"] : 0;
            $this->doccomnum = isset($array["doccomnum"]) && is_numeric($array["doccomnum"]) ? (int)$array["doccomnum"] : 0;
            $this->docview = isset($array["docview"]) && is_numeric($array["docview"]) ? (int)$array["docview"] : 0;
            $this->doccreatetime = isset($array["doccreatetime"])  ? strtotime($array["doccreatetime"]) : -1;
            $this->docupdatetime = isset($array["docupdatetime"])  ? strtotime($array["docupdatetime"]) : -1;
            $this->docsort = isset($array["docsort"]) && is_numeric($array["docsort"]) ? (int)$array["docsort"] : 0;
            $this->docvalid = isset($array["docvalid"]) && is_numeric($array["docvalid"]) ? (int)$array["docvalid"] : 0;
        }
        if($bo) $this->GetType();
        else $this->Type = false;
    }

    public function GetType($id = 0){
        $this->typeid = $id > 0 ? $id : 0;
        $this->Type = $this->typeid > 0 ? Type::GetType($this->typeid) : false;
    }

    public function MakeJson(){
        $str = "{ \"docid\": \"".$this->docid."\", \"typeid\": \"".$this->typeid."\", \"doctitle\": \"".$this->doctitle."\", \"docsubtitle\": \"".$this->docsubtitle."\", \"docstgnum\": \""
            .$this->docstgnum."\", \"doccomnum\": \"".$this->doccomnum."\", \"docview\": \"".$this->docview."\", \"doccreatetime\": \"".date("Y-m-d H:i:s", $this->doccreatetime)
            ."\", \"docupdatetime\": \"".date("Y-m-d H:i:s", $this->docupdatetime)."\", \"docsort\": \"".$this->docsort."\", \"docvalid\": \"".$this->docvalid."\", \"Type\": "
            .($this->Type ? $this->Type->MakeJson() : "{ }")." }";
        return $str;
    }

    public static function Exists($name){
        if(!isset($name)) return true;
        $str = "select count(*) from Documents where ";
        $paras = array();
        if(is_int($name)){
            $str .= "docid = :docid; ";
            $paras[":docid"] = $name;
        }
        else{
            $str .= "doctitle = :doctitle; ";
            $paras[":doctitle"] = $name;
        }
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($doc){
        if(!is_a($doc, "Document")) return false;
        $str = "insert into Documents (typeid, doctitle, docsubtitle, docsort, docvalid) values (:typeid, :doctitle, :docsubtitle, :docsort, :docvalid); ";
        $paras = array(
            ":typeid" => $doc->typeid, ":doctitle" => $doc->doctitle, ":docsubtitle" => $doc->docsubtitle, ":docsort" => $doc->docsort, 
            ":docvalid" => $doc->docvalid
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function Update(){
        if(!is_a($doc, "Document")) return false;
        $str = "update Documents set typeid = :typeid, doctitle = :doctitle, docsubtitle = :docsubtitle, docupdatetime = :docupdatetime, "
            ."docsort = :docsort, docvalid = :docvalid where docid = :docid ";
        $paras = array(
            ":typeid" => $doc->typeid, ":doctitle" => $doc->doctitle, ":docsubtitle" => $doc->docsubtitle, ":docupdatetime" => time(), 
            ":docsort" => $doc->docsort, ":docvalid" => $doc->docvalid, ":docid" => $doc->docid
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function GetDocs($typeid = -1, $valid = -1, $pagenum = 1, $pagesize = 0, $order = "sort"){
        $typeid = is_int($typeid) ? $typeid : -1;
        $valid = is_int($valid) ? $valid : -1;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $count = "select count(*) as count ";
        $select = "select docid, typeid, doctitle, docsubtitle, docstgnum, doccomnum, docview, doccreatetime, docupdatetime, docsort, docvalid ";
        $where = "from Documents where 1 = 1 ";
        $paras = array();
        if ($typeid < -1) $where .= "and typeid > 0 ";
        else if($typeid > -1){
            $where .= "and typeid = :typeid ";
            $paras[":typeid"] = $typeid;
        }
        if($valid < -1) $where .= "and docvalid > 0 ";
        else if($valid > -1){
            $where .= "and docvalid = :docvalid ";
            $paras[":docvalid"] = $valid;
        }
        $count .= $where."; ";
        switch($order){
            case "docstgnum":
                $where .= "order by docstgnum desc, docsort desc, docid desc ";
                break;
            case "doccomnum":
                $where .= "order by doccomnum desc, docsort desc, docid desc ";
                break;
            case "docview":
                $where .= "order by docview desc, docsort desc, docid desc ";
                break;
            default:
                $where .= "order by docsort desc, docid desc ";
                break;
        }
        $select .= $where;
        if($pagenum > 0 && $pagesize > 0)
            $select .= "limit ".($pagesize > 1 ? ($pagenum - 1) * $pagesize : 0).", ".$pagesize."; ";
        else $select .= "; ";
        $en = new Entity();
        $list = new Resaults();
        $res = $en->Query($count, $paras);
        if($res) $list->page->MakePage((int)$res[0]["count"], $pagenum, $pagesize);
        $res = $en->Query($select, $paras);
        if($res) foreach($res as $key => $value) $list->list[] = new Document($value);
        return $list;
    }

    public static function GetDoc($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select docid, typeid, doctitle, docsubtitle, docstgnum, doccomnum, docview, doccreatetime, docupdatetime, docsort, docvalid "
            ."from Documents where docid = :docid; ";
        $paras = array(":docid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
        return new Document($res);
    }

    public static function Valid($id, $valid = -1){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $valid = is_int($valid) ? $id : -1;
        $str = "update Documents set ";
        $paras = array();
        if($valid == -1){
            $str .= "docvalid = case when docvalid = 0 then 1 else 1 end where docid = :docid; ";
            $paras = array(":docid" => $id);
        }
        else {
            $str .= "docvalid = :docvalid where docid = :docid; ";
            $paras = array(":docid" => $id, ":typevalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    public static function StageAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return false;
        $str = "update Documents set docstgnum = docstgnum + 1 where docid = :docid; ";
        $paras = array(":docid" => $id);
        return (new Entity())->Exec($str, $paras);
    }

    public static function ViewAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return false;
        $str = "update Documents set docview = docview + 1 where docid = :docid; ";
        $paras = array(":docid" => $id);
        return (new Entity())->Exec($str, $paras);
    }

    public static function CommAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return false;
        $str = "update Documents set doccomnum = doccomnum + 1 where docid = :docid; ";
        $paras = array(":docid" => $id);
        return (new Entity())->Exec($str, $paras);
    }
}
?>