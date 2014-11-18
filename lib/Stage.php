<?php 
include_once("Entity.php");
include_once("Type.php");
include_once("Document.php");
class Stage{
    var $stgid; // int primary key auto_increment,
    var $docid; // int references Documents(docid) on delete cascade on update cascade,
    var $stgtitle; // nvarchar(15) not null,
    var $stgsubtitle; // nvarchar(50) default "",
    var $stgcontent; // nvarchar(12000) not null,
    var $stgview; // int default 0,
    var $stgcomnum; // int default 0,
    var $stgcreatetime; // timestamp default current_timestamp,
    var $stgupdatetime; // timestamp default current_timestamp on update current_timestamp,
    var $stgsort; // int default 0,
    var $stgvalid; // int default 1

    var $Dococument;

    public function __construct($array = null, $bo = false){
        if($array == null || !is_array($array)){
            $this->stgid = 0;
            $this->docid = 0;
            $this->stgtitle = "";
            $this->stgsubtitle = "";
            $this->stgcontent = "";
            $this->stgview = 0;
            $this->stgcomnum = 0;
            $this->stgcreatetime = -1;
            $this->stgupdatetime = -1;
            $this->stgsort = 0;
            $this->stgvalid = 0;
        }
        else{
            $this->stgid = isset($array["stgid"]) && is_numeric($array["stgid"]) ? (int)$array["stgid"] : 0;
            $this->docid = isset($array["docid"]) && is_numeric($array["docid"]) ? (int)$array["docid"] : 0;
            $this->stgtitle = isset($array["stgtitle"]) ? $array["stgtitle"] : "";
            $this->stgsubtitle = isset($array["stgsubtitle"]) ? $array["stgsubtitle"] : "";
            $this->stgcontent = isset($array["stgcontent"]) ? $array["stgcontent"] : "";
            $this->stgview = isset($array["stgview"]) && is_numeric($array["stgview"]) ? (int)$array["stgview"] : 0;
            $this->stgcomnum = isset($array["stgcomnum"]) && is_numeric($array["stgcomnum"]) ? (int)$array["stgcomnum"] : 0;
            $this->stgcreatetime = isset($array["stgcreatetime"])  ? strtotime($array["stgcreatetime"]) : -1;
            $this->stgupdatetime = isset($array["stgupdatetime"])  ? strtotime($array["stgupdatetime"]) : -1;
            $this->stgsort = isset($array["stgsort"]) && is_numeric($array["stgsort"]) ? (int)$array["stgsort"] : 0;
            $this->stgvalid = isset($array["stgvalid"]) && is_numeric($array["stgvalid"]) ? (int)$array["stgvalid"] : 0;
        }
        if($bo){
            $this->GetDoc();
        }
        else{
            $this->Dococument = false;
        }
    }

    public function GetDoc($id = 0){
        $this->docid = $id > 0 ? $id : 0;
        $this->Document = $this->docid > 0 ? Document::GetDoc($this->docid) : false;
    }

    public function MakeJson(){
        $str = "{ \"stgid\": \"".$this->stgid."\", \"docid\": \"".$this->docid."\", \"stgpid\": \"".$this->stgpid."\", \"stgtitle\": \"".$this->stgtitle."\", \"stgsubtitle\": \""
            .$this->stgsubtitle."\", \"stgcontent\": \"".$this->stgcontent."\", \"stgview\": \"".$this->stgview."\", \"stgcomnum\": \"".$this->stgcomnum."\", \"stgcreatetime\": \""
            .date("Y-m-d H:i:s", $this->stgcreatetime)."\", \"stgupdatetime\": \"".date("Y-m-d H:i:s", $this->stgupdatetime)."\", \"stgsort\": \"".$this->stgsort."\", \"stgvalid\": \""
            .$this->stgvalid."\", \"Document\": ".($this->Document ? $this->Document->MakeJson() : "{ }")." }";
        return $str;
    }

    public static function Exists($name){
        if(!isset($name)) return true;
        $str = "select count(*) from Stages where ";
        $paras = array();
        if(is_int($name)){
            $str .= "stgid = :stgid; ";
            $paras[":stgid"] = $name;
        }
        else{
            $str .= "stgtitle = :stgtitle; ";
            $paras[":stgtitle"] = $name;
        }
        $en = new Entity();
        $num = $en->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($stage){
        if(!is_a($stage, "Stage")) return false;
        $str = "insert into Stages (docid, stgtitle, stgsubtitle, stgcontent, stgsort, stgvalid) values (:docid, :stgtitle, :stgsubtitle, :stgcontent, :stgsort, :stgvalid); ";
        $paras = array(
            ":docid" => $stage->docid, ":stgtitle" => $stage->stgtitle, ":stgsubtitle" => $stage->stgsubtitle, ":stgcontent" => $stage->stgcontent, 
            ":stgsort" => $stage->stgsort, ":stgvalid" => $stage->stgvalid
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function Update($stage){
        if(!is_a($doc, "Document")) return false;
        $str = "update Stages set docid = :docid, stgtitle = :stgtitle, stgsubtitle = :stgsubtitle, stgcontent = :stgcontent, stgupdatetime = :stgupdatetime, "
            ."stgsort = :stgsort, stgvalid = :stgvalid where stgid = :stgid ";
        $paras = array(
            ":docid" => $stage->docid, ":stgtitle" => $stage->stgtitle, ":stgsubtitle" => $stage->stgsubtitle, ":stgcontent" => $stage->stgcontent, 
            ":stgupdatetime" => $stage->stgupdatetime, ":stgsort" => $stage->stgsort, ":stgvalid" => $stage->stgvalid, ":stgid" => $stage->stgid
        );
        return (new Entity())->Exec($str, $paras);
    }

    public static function GetStages($docid = -1, $valid = -1, $pagenum = 1, $pagesize = 0, $order = "sort"){
        $docid = is_int($docid) ? $docid : -1;
        $valid = is_int($valid) ? $valid : -1;
        $pagenum = is_int($pagenum) ? $pagenum : 1;
        $pagesize = is_int($pagesize) ? $pagesize : 0;
        $count = "select count(*) as count ";
        $select = "select stgid, docid, stgtitle, stgsubtitle, stgcontent, stgview, stgcomnum, stgcreatetime, stgupdatetime, stgsort, stgvalid ";
        $where = "from Stages where 1 = 1 ";
        $paras = array();
        if ($docid < -1) $where .= "and docid > 0 ";
        else if($docid > -1){ 
            $where .= "and docid = :docid ";
            $paras[":docid"] = $docid;
        }
        if($valid < -1) $where .= "and stgvalid > 0 ";
        else if($valid > -1){
            $where .= "and stgvalid = :stgvalid ";
            $paras[":stgvalid"] = $valid;
        }
        $count .= $where."; ";
        switch($order){
            case "stgview":
                $where .= "order by stgview desc, stgsort desc, stgid desc ";
                break;
            case "stgcomnum":
                $where .= "order by stgcomnum desc, stgsort desc, stgid desc ";
                break;
            default: 
                $where .= "order by stgsort desc, stgid desc ";
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
        if($res) foreach($res as $key => $value) $list->list[] = new Stage($value);
        return $list;
    }

    public static function GetStage($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select stgid, docid, stgtitle, stgsubtitle, stgcontent, stgview, stgcomnum, stgcreatetime, stgupdatetime, stgsort, stgvalid "
            ."from Stages where stgid = :stgid; ";
        $paras = array(":stgid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
        return new Stage($res);
    }

    public static function Valid($id, $valid = -1){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $valid = is_int($valid) ? $id : -1;
        $str = "update Stages set ";
        $paras = array();
        if($valid == -1){
            $str .= "stgvalid = case when stgvalid = 0 then 1 else 1 end where stgid = :stgid; ";
            $paras = array(":stgid" => $id);
        }
        else {
            $str .= "stgvalid = :stgvalid where stgid = :stgid; ";
            $paras = array(":stgid" => $id, ":stgvalid" => $valid);
        }
        $en = new Entity();
        return $en->Exec($str, $paras);
    }

    public static function ViewAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return false;
        $str = "update Stages set stgview = stgview + 1 where stgid = :stgid; ";
        $paras = array(":stgid" => $id);
        return (new Entity())->Exec($str, $paras);
    }

    public static function CommAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return false;
        $str = "update Stages set stgcomnum = stgcomnum + 1 where stgid = :stgid; ";
        $paras = array(":stgid" => $id);
        return (new Entity())->Exec($str, $paras);
    }
}
?>