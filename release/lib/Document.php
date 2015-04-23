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

    var $type;

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
        $this->type = $bo && $this->typeid > 0 ? Type::GetType($this->typeid) : null;
    }

    public static function Exists($title, $execpt = null){
        if(!$title) return true;
        $str = "select count(*) from Documents where doctitle = :doctitle";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and docid != :docid";
            $paras[":docid"] = $execpt;
        }
        $paras[":doctitle"] = $title;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($doc){
        if(!$doc instanceof Documents) return new Message("对象类型不正确");
        if(Documents::Exists($doc->doctitle)) return new Message("要添加的文章名称已存在");
        $str = "insert into Documents (typeid, doctitle, docsubtitle, docsort, docvalid) values "
            ."(:typeid, :doctitle, :docsubtitle, :docsort, :docvalid); ";
        $str .= "select docid, typeid, doctitle, docsubtitle, docstgnum, doccomnum, docview, doccreatetime, "
            ."docupdatetime, docsort, docvalid from Documents where docid = @@identity; ";
        $paras = array(
            ":typeid" => $doc->typeid, ":doctitle" => $doc->doctitle, ":docsubtitle" => $doc->docsubtitle, 
            ":docsort" => $doc->docsort, ":docvalid" => $doc->docvalid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("创建成功", true, new Document($obj[1][0])) : new Message("创建失败");
    }

    public static function Update($doc){
        if(!$doc instanceof Documents) return new Message("对象类型不正确");
        if(Documents::Exists($doc->doctitle, $doc->docid)) return new Message("要添加的文章名称已存在");
        $str = "update Documents set typeid = :typeid, doctitle = :doctitle, docsubtitle = :docsubtitle, "
            ."docupdatetime = :docupdatetime, docsort = :docsort, docvalid = :docvalid where docid = :docid ";
        $str .= "select docid, typeid, doctitle, docsubtitle, docstgnum, doccomnum, docview, doccreatetime, "
            ."docupdatetime, docsort, docvalid from Documents where docid = :docid; ";
        $paras = array(
            ":typeid" => $doc->typeid, ":doctitle" => $doc->doctitle, ":docsubtitle" => $doc->docsubtitle, 
            ":docupdatetime" => time(), ":docsort" => $doc->docsort, ":docvalid" => $doc->docvalid, ":docid" => $doc->docid
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
        $search->title = isset($search->title) ? strval($search->title) : "";
        $search->subtitle = isset($search->subtitle) ? strval($search->subtitle) : "";
        $search->typeid = isset($search->typeid) && is_numeric($search->typeid) ? (int)$search->typeid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $search->order = "sort"
        $count = "select count(*) as count ";
        $select = "select docid, typeid, doctitle, docsubtitle, docstgnum, doccomnum, docview, doccreatetime, "
            ."docupdatetime, docsort, docvalid ";
        $where = "from Documents where 1 = 1 ";
        $paras = array();
        if($search->title != ""){
            $where .= "and ( ";
            $arr = explode(" ", $search->title);
            for($i = 0, $z = count($arr); $i < $z; $i++)
            {
                $where .= "doctitle like concat(\"%\", :str_a_".$i.", \"%\") ";
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
                $where .= "docsubtitle like concat(\"%\", :str_b_".$i.", \"%\") ";
                if ($i < $z - 1) $where .="or ";
                $paras[":str_b_".$i] = $arr[$i];
            }
            $where .= ") ";
        }
        if($search->typeid > 0){ 
            $where .= "and typeid = :typeid ";
            $paras[":typeid"] = ($search->typeid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and docvalid = :docvalid ";
            $paras[":docvalid"] = $search->valid;
        }
        $count .= $where."; ";
        switch($search->order){
            case "stgnum":
                $where .= "order by docstgnum desc, docsort desc, docid desc ";
                break;
            case "comm":
                $where .= "order by doccomnum desc, docsort desc, docid desc ";
                break;
            case "view":
                $where .= "order by docview desc, docsort desc, docid desc ";
                break;
            case "update":
                $where .= "order by docupdatetime desc, docsort desc, docid desc ";
                break;
            default:
                $where .= "order by docsort desc, docid desc ";
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
        foreach($res[1] as $key => $value) $list[] = new Documents($value, $deep);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Get($id, $deep = false){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select docid, typeid, doctitle, docsubtitle, docstgnum, doccomnum, docview, doccreatetime, "
            ."docupdatetime, docsort, docvalid from Documents where docid = :docid; ";
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

    public static function StageAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return new Message("修改失败");
        $str = "update Documents set docstgnum = docstgnum + 1 where docid = :docid; "
            ."select docstgnum from Documents where docid = :docid; ";
        $paras = array(":docid" => $id);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }

    public static function ViewAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return new Message("修改失败");
        $str = "update Documents set docview = docview + 1 where docid = :docid; "
            ."select docview from Documents where docid = :docid; ";
        $paras = array(":docid" => $id);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }

    public static function CommAdd($id){
        $id = is_int($id) ? (int)$id : 0;
        if($id < 1) return new Message("修改失败");
        $str = "update Documents set doccomnum = doccomnum + 1 where docid = :docid; ";
            ."select doccomnum from Documents where docid = :docid; ";
        $paras = array(":docid" => $id);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }
}
?>