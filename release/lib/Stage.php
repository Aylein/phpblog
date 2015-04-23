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
        $this->Document = $bo && $this->docid > 0 ? Document::Get($this->docid) : null;
    }

    //检查存在
    public static function Exists($title, $docid, $execpt = null){
        if(!$title || !$docid) return true;
        $str = "select count(*) from Stages where stgtitle = :stgtitle and docid = :docid";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and typeid != :typeid";
            $paras[":typeid"] = $execpt;
        }
        $paras[":stgtitle"] = $title;
        $paras[":docid"] = $docid;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($stage, $deep = false){
        if(!$stage instanceof Stage) return new Message("对象类型不正确");
        if(Stage::Exists($stage->stgtitle, $stage->docid)) return new Message("要添加的章节名称已存在");
        $str = "insert into Stages (docid, stgtitle, stgsubtitle, stgcontent, stgsort, stgvalid) values "
            ."(:docid, :stgtitle, :stgsubtitle, :stgcontent, :stgsort, :stgvalid); ";
        $str .= "select stgid, docid, stgtitle, stgsubtitle, stgcontent, stgview, stgcomnum, stgcreatetime, "
            ."stgupdatetime, stgsort, stgvalid from Stages where stgid = @@identity; ";
        $paras = array(
            ":docid" => $stage->docid, ":stgtitle" => $stage->stgtitle, ":stgsubtitle" => $stage->stgsubtitle, 
            ":stgcontent" => $stage->stgcontent, ":stgsort" => $stage->stgsort, ":stgvalid" => $stage->stgvalid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Stage($en[1][0], $deep)) : new Message("添加失败");
    }

    public static function Update($stage, $deep = false){
        if(!$stage instanceof Stage) return new Message("对象类型不正确");
        if(Stage::Exists($stage->stgtitle, $stage->docid, $stage->stgid)) return new Message("要添加的章节名称已存在");
        $str = "update Stages set docid = :docid, stgtitle = :stgtitle, stgsubtitle = :stgsubtitle, stgcontent = :stgcontent, "
            ."stgupdatetime = :stgupdatetime, stgsort = :stgsort, stgvalid = :stgvalid where stgid = :stgid ";
        $str .= "select stgid, docid, stgtitle, stgsubtitle, stgcontent, stgview, stgcomnum, stgcreatetime, "
            ."stgupdatetime, stgsort, stgvalid from Stages where stgid = :stgid; ";
        $paras = array(
            ":docid" => $stage->docid, ":stgtitle" => $stage->stgtitle, ":stgsubtitle" => $stage->stgsubtitle, 
            ":stgcontent" => $stage->stgcontent, ":stgupdatetime" => $stage->stgupdatetime, ":stgsort" => $stage->stgsort, 
            ":stgvalid" => $stage->stgvalid, ":stgid" => $stage->stgid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new Stage($en[1][0], $deep)) : new Message("修改失败");
    }

    public static function Add_Update($stage, $deep = false){
        if(!$stage instanceof Stage) return new Message("对象类型不正确");
        return $stage->stgid > 0 ? Stage::Update($stage, $deep) : Stage::Add($stage, $deep);
    }

    public static function GetAll($search = null, $deep = false){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->title = isset($search->title) ? strval($search->title) : "";
        $search->subtitle = isset($search->subtitle) ? strval($search->subtitle) : "";
        $search->docid = isset($search->docid) && is_numeric($search->docid) ? (int)$search->docid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $search->$order = isset($search->order) ? strval($search->order) : "";
        $count = "select count(*) as count ";
        $select = "select stgid, docid, stgtitle, stgsubtitle, stgcontent, stgview, stgcomnum, stgcreatetime, "
            ."stgupdatetime, stgsort, stgvalid ";
        $where = "from Stages where 1 = 1 ";
        $paras = array();
        if($search->title != ""){
            $where .= "and ( ";
            $arr = explode(" ", $search->title);
            for($i = 0, $z = count($arr); $i < $z; $i++)
            {
                $where .= "stgtitle like concat(\"%\", :str_a_".$i.", \"%\") ";
                if ($i < $z - 1) $where .="or ";
                $paras[":str_a_".$i] = $arr[$i];
            }
            $where .= ") ";
        }
        if($search->subtitle != ""){
            $where .= "and ( ";
            $arr = explode(" ", $search->subtitle);
            for($i = 0, $z = count($arr); $i < $z; $i++)
            {
                $where .= "stgsubtitle like concat(\"%\", :str_b_".$i.", \"%\") ";
                if ($i < $z - 1) $where .="or ";
                $paras[":str_b_".$i] = $arr[$i];
            }
            $where .= ") ";
        }
        if($search->docid > 0){ 
            $where .= "and docid = :docid ";
            $paras[":docid"] = ($search->docid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and stgvalid = :stgvalid ";
            $paras[":stgvalid"] = $search->valid;
        }
        $count .= $where."; ";
        switch($search->$order){
            case "view":
                $where .= "order by stgview desc, stgsort desc, stgid desc ";
                break;
            case "comm":
                $where .= "order by stgcomnum desc, stgsort desc, stgid desc ";
                break;
            default: 
                $where .= "order by stgsort desc, stgid desc ";
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
        foreach($res[1] as $key => $value) $list[] = new Stage($value, $deep);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id, $deep = false){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select stgid, docid, stgtitle, stgsubtitle, stgcontent, stgview, stgcomnum, stgcreatetime, "
            ."stgupdatetime, stgsort, stgvalid from Stages where stgid = :stgid; ";
        $paras = array(":stgid" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new Stage($res, $deep);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Stages set ";
        $paras = array();
        if(!$valid){
            $str .= "stgvalid = case when stgvalid = 0 then 1 else 1 end where stgid = :stgid; ";
            $paras = array(":stgid" => $id);
        }
        else {
            $str .= "stgvalid = :stgvalid where stgid = :stgid; ";
            $paras = array(":stgid" => $id, ":stgvalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }

    public static function ViewAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return new Message("修改失败");
        $str = "update Stages set stgview = stgview + 1 where stgid = :stgid; "
            ."select stgview from Stages where stgid = :stgid; ";
        $paras = array(":stgid" => $id);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }

    public static function CommAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return new Message("修改失败");
        $str = "update Stages set stgcomnum = stgcomnum + 1 where stgid = :stgid; ";
            ."select stgcomnum from Stages where stgid = :stgid; ";
        $paras = array(":stgid" => $id);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }
}
?>