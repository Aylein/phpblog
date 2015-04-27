<?php 
include_once("Entity.php");
include_once("Type.php");
include_once("User.php");
class Stage{
    var $stgid; // int primary key auto_increment,
    var $stgpid; 
    var $typeid;
    var $userid;
    var $stgtitle; // nvarchar(15) not null,
    var $stgsubtitle; // nvarchar(50) default "",
    var $stgtype; //char(5), #stage docum,
    var $stgnum;
    var $stgview; // int default 0,
    var $stgcomnum; // int default 0,
    var $stgcreatetime; // timestamp default current_timestamp,
    var $stgupdatetime; // timestamp default current_timestamp on update current_timestamp,
    var $stgsort; // int default 0,
    var $stgvalid; // int default 1

    var $type;
    var $user;

    public function __construct($array = null, $bo = false){
        if($array == null || !is_array($array)){
            $this->stgid = 0;
            $this->stgpid = 0;
            $this->typeid = 0;
            $this->userid = 0;
            $this->stgtitle = "";
            $this->stgsubtitle = "";
            $this->stgtype = "";
            $this->stgview = 0;
            $this->stgcomnum = 0;
            $this->stgcreatetime = -1;
            $this->stgupdatetime = -1;
            $this->stgsort = 0;
            $this->stgvalid = 0;
        }
        else{
            $this->stgid = isset($array["stgid"]) && is_numeric($array["stgid"]) ? (int)$array["stgid"] : 0;
            $this->stgpid = isset($array["stgpid"]) && is_numeric($array["stgpid"]) ? (int)$array["stgpid"] : 0;
            $this->typeid = isset($array["typeid"]) && is_numeric($array["typeid"]) ? (int)$array["typeid"] : 0;
            $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
            $this->stgtitle = isset($array["stgtitle"]) ? $array["stgtitle"] : "";
            $this->stgsubtitle = isset($array["stgsubtitle"]) ? $array["stgsubtitle"] : "";
            $this->stgtype = isset($array["stgtype"]) ? $array["stgtype"] : "";
            $this->stgnum = isset($array["stgnum"]) && is_numeric($array["stgnum"]) ? (int)$array["stgnum"] : 0;
            $this->stgview = isset($array["stgview"]) && is_numeric($array["stgview"]) ? (int)$array["stgview"] : 0;
            $this->stgcomnum = isset($array["stgcomnum"]) && is_numeric($array["stgcomnum"]) ? (int)$array["stgcomnum"] : 0;
            $this->stgcreatetime = isset($array["stgcreatetime"])  ? strtotime($array["stgcreatetime"]) : -1;
            $this->stgupdatetime = isset($array["stgupdatetime"])  ? strtotime($array["stgupdatetime"]) : -1;
            $this->stgsort = isset($array["stgsort"]) && is_numeric($array["stgsort"]) ? (int)$array["stgsort"] : 0;
            $this->stgvalid = isset($array["stgvalid"]) && is_numeric($array["stgvalid"]) ? (int)$array["stgvalid"] : 0;
        }
        $this->type = $bo && $this->typeid > 0 ? Type::Get($this->typeid) : null;
        $this->user = $bo && $this->userid > 0 ? User::Get($this->userid) : null;
    }

    //检查存在
    public static function Exists($title, $typeid, $stgpid = 0, $execpt = null){
        if(!$title || !$typeid) return true;
        $str = "select count(*) from Stages where stgtitle = :stgtitle and stgpid = :stgpid and typeid = ?typeid ";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and stgid != :stgid";
            $paras[":stgid"] = $execpt;
        }
        $paras[":stgtitle"] = $title;
        $paras[":stgpid"] = $stgpid;
        $paras[":typeid"] = $typeid;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($stage, $deep = false){
        if(!$stage instanceof Stage) return new Message("对象类型不正确");
        if(Stage::Exists($stage->stgtitle, $stage->typeid, $stage->stgpid)) return new Message("要添加的章节名称已存在");
        $str = "insert into Stages (stgpid, typeid, userid, stgtitle, stgsubtitle, stgtype, stgsort, stgvalid) values "
            ."(:stgpid, :typeid, :userid, :stgtitle, :stgsubtitle, :stgtype, :stgsort, :stgvalid); ";
        $str .= "select stgid, stgpid, typeid, userid, stgtitle, stgsubtitle, stgtype, stgnum, stgview, stgcomnum, stgcreatetime, "
            ."stgupdatetime, stgsort, stgvalid from Stages where stgid = @@identity; ";
        $paras = array(
            ":stgpid" => $stage->stgpid, ":typeid" => $stage->typeid, ":userid" => $stage->userid, ":stgtitle" => $stage->stgtitle, 
            ":stgsubtitle" => $stage->stgsubtitle, ":stgtype" => $stage->stgtype, ":stgsort" => $stage->stgsort, 
            ":stgvalid" => $stage->stgvalid
        );
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("添加成功", true, new Stage($en[1][0], $deep)) : new Message("添加失败");
    }

    public static function Update($stage, $deep = false){
        if(!$stage instanceof Stage) return new Message("对象类型不正确");
        if(Stage::Exists($stage->stgtitle, $stage->stgpid, $stage->stgid)) return new Message("要添加的章节名称已存在");
        $str = "update Stages set stgpid = :stgpid, typeid = :typeid, userid = :userid, stgtitle = :stgtitle, stgsubtitle = :stgsubtitle, "
            ."stgtype = :stgtype, stgupdatetime = :stgupdatetime, stgsort = :stgsort, stgvalid = :stgvalid "
            ."where stgid = :stgid ";
        $str .= "select stgid, stgpid, typeid, userid, stgtitle, stgsubtitle, stgtype, stgnum, stgview, stgcomnum, stgcreatetime, "
            ."stgupdatetime, stgsort, stgvalid from Stages where stgid = :stgid; ";
        $paras = array(
            ":stgpid" => $stage->stgpid, ":typeid" => $stage->typeid, ":userid" => $stage->userid, ":stgtitle" => $stage->stgtitle, 
            ":stgsubtitle" => $stage->stgsubtitle, ":stgtype" => $stage->stgtype, ":stgupdatetime" => $stage->stgupdatetime, 
            ":stgsort" => $stage->stgsort, ":stgvalid" => $stage->stgvalid, ":stgid" => $stage->stgid
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
        $search->stgpid = isset($search->stgpid) && is_numeric($search->stgpid) ? (int)$search->stgpid : 0;
        $search->typeid = isset($search->typeid) && is_numeric($search->typeid) ? (int)$search->typeid : 0;
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->stagenim = isset($search->stagenim) && is_numeric($search->stagenim) ? (int)$search->stagenim : 0;
        $search->stagemax = isset($search->stagemax) && is_numeric($search->stagemax) ? (int)$search->stagemax : 0;
        $search->viewmin = isset($search->viewmin) && is_numeric($search->viewmin) ? (int)$search->viewmin : 0;
        $search->viewmax = isset($search->viewmax) && is_numeric($search->viewmax) ? (int)$search->viewmax : 0;
        $search->commmin = isset($search->commmin) && is_numeric($search->commmin) ? (int)$search->commmin : 0;
        $search->commmax = isset($search->commmax) && is_numeric($search->commmax) ? (int)$search->commmax : 0;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $search->$order = isset($search->order) ? strval($search->order) : "";
        $count = "select count(*) as count ";
        $select = "select stgid, stgpid, typeid, userid, stgtitle, stgsubtitle, stgtype, stgnum, stgview, stgcomnum, stgcreatetime, "
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
        if($search->stagenim > 0){ 
            $where .= "and stgnum >= :stgnum ";
            $paras[":stgnum"] = ($search->stagenim;
        }
        if($search->stagemax > 0){ 
            $where .= "and stgnum <= :stgnum ";
            $paras[":stgnum"] = ($search->stagemax;
        }
        if($search->viewmin > 0){ 
            $where .= "and stgview >= :stgview ";
            $paras[":stgview"] = ($search->viewmin;
        }
        if($search->viewmax > 0){ 
            $where .= "and stgview <= :stgview ";
            $paras[":stgview"] = ($search->viewmax;
        }
        if($search->commmin > 0){ 
            $where .= "and stgcomnum >= :stgcomnum ";
            $paras[":stgcomnum"] = ($search->commmin;
        }
        if($search->commmax > 0){ 
            $where .= "and stgcomnum <= :stgcomnum ";
            $paras[":stgcomnum"] = ($search->commmax;
        }
        if($search->typeid > 0){ 
            $where .= "and typeid = :typeid ";
            $paras[":typeid"] = ($search->typeid;
        }
        if($search->stgpid > 0){ 
            $where .= "and stgpid = :stgpid ";
            $paras[":stgpid"] = ($search->stgpid;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and stgvalid = :stgvalid ";
            $paras[":stgvalid"] = $search->valid;
        }
        $count .= $where."; ";
        switch($search->$order){
            case "stage":
                $where .= "order by stgnum desc, stgsort desc, stgid desc ";
                break;
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
        $str = "select stgid, stgpid, typeid, userid, stgtitle, stgsubtitle, stgtype, stgnum, stgview, stgcomnum, stgcreatetime, "
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

    public static function AddNum($id, $type, $num = null, $addtype = "add"){
        switch($type){
            case "stgnum": return Stage::StageAdd($id, $num, $addtype);
            case "viewnum": return Stage::ViewAdd($id, $num, $addtype);
            case "commnum": return Stage::CommAdd($id, $num, $addtype);
            default: return new Message("修改失败");
        }
    }

    public static function StageAdd($id, $num = null, $addtype = "add"){ //add set upset auto
        $num = isset($num) && is_numeric($num) ? (int)$num : -1;
        $addtype = $num > -1 && $addtype != "set" && $addtype != "add" ? "auto" : $addtype;
        if($id < 1) return new Message("修改失败");
        $str = "update Stages set stgnum = ";
        $paras = array();
        switch($addtype){
            case "auto": $str .= "(select count(*) from Stage where stgpid = :stgid) "; break;
            case "add": $str .= "stgnum + 1 "; break;
            case "upset": $str .= "stgnum - 1 "; break;
            case "set": 
                $str .= "stgnum = :stgnum ";
                $paras[":stgnum"] = $num;
                break;
        }
        "where stgid = :stgid; ";
        $str .= "select stgnum from Stages where stgid = :stgid; ";
        $paras[":stgid"] = $id;
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }

    public static function ViewAdd($id, $num = null, $addtype = "add"){ //add upset set
        $id = is_int($id) ? (int)$id : 0;
        $num = isset($num) && is_numeric($num) ? (int)$num : -1;
        $addtype = $num > -1 && $addtype != "set" && $addtype != "add" ? "add" : $addtype;
        if($id < 1) return new Message("修改失败");
        $str = "update Stages set stgview = ";
        $paras = array();
        switch($addtype){
            case "add": $str .= "stgview + 1 "; break;
            case "upset": $str .= "stgview - 1 "; break;
            case "set": 
                $str .= "stgview = :stgview ";
                $paras[":stgview"] = $num;
                break;
        }
        "where stgid = :stgid; ";
        $str .= "select stgview from Stages where stgid = :stgid; ";
        $paras[":stgid"] = $id;
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }

    public static function CommAdd($id, $num = null, $addtype = "add"){ //add upset set
        $id = is_int($id) ? (int)$id : 0;
        $num = isset($num) && is_numeric($num) ? (int)$num : -1;
        $addtype = $num > -1 && $addtype != "set" && $addtype != "add" ? "add" : $addtype;
        if($id < 1) return new Message("修改失败");
        $str = "update Stages set stgcomnum = ";
        $paras = array();
        switch($addtype){
            case "add": $str .= "stgcomnum + 1 "; break;
            case "upset": $str .= "stgcomnum - 1 "; break;
            case "set": 
                $str .= "stgcomnum = :stgcomnum ";
                $paras[":stgcomnum"] = $num;
                break;
        }
        "where stgid = :stgid; ";
        $str .= "select stgcomnum from Stages where stgid = :stgid; ";
        $paras[":stgid"] = $id;
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, (int)$en[1][0]) : new Message("修改失败");
    }
}
?>