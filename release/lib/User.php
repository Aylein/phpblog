<?php
include_once("Entity.php");
include_once("Main.php");
class User{
    var $userid;
    var $username;
    var $userpass;
    var $userimg;
    var $usertype;
    var $usercreatetime;
    var $usersort;
    var $uservalid;

    public function __construct($array = null){
        if($array == null || !is_array($array)){
            $this->userid = 0;
            $this->username = "";
            $this->userpass = "";
            $this->userimg = "";
            $this->usertype = "visit"; //visit admin guest
            $this->usercreatetime = -1;
            $this->usersort = 0;
            $this->uservalid = 1;
        }
        else{
            $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
            $this->username = isset($array["username"])  ? $array["username"] : "";
            $this->userpass = isset($array["userpass"])  ? $array["userpass"] : "";
            $this->userimg = isset($array["userimg"])  ? $array["userimg"] : User::makeImg();
            $this->usertype = isset($array["usertype"])  ? $array["usertype"] : "visit";
            $this->usercreatetime = isset($array["usercreatetime"])  ? strtotime($array["usercreatetime"]) : -1;
            $this->usersort = isset($array["usersort"]) && is_numeric($array["usersort"]) ? (int)$array["usersort"] : 0;
            $this->uservalid = isset($array["uservalid"]) && is_numeric($array["uservalid"]) ? (int)$array["uservalid"] : 1;
        }
    }
    
    private static function getPass($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select userpass from Users where userid = :userid; ";
        $paras = array(":userid" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new User($res);
    }
    
    public static function getUserTypes(){ return Array("visit", "admin", "guest"); }

    public static function makePass($pass){ return md5($pass."shikinami"); }
    
    public static function makeImg(){ return "images/ac/ac_".rand(1, 50).".png"; }
    
    public static function Exists($name, $pass, $execpt = null){
        if(!$name || !$pass) return true;
        $str = "select count(*) from Users where (username = :username or userpass = :userpass)";
        $paras = array();
        if($execpt != null && is_int($execpt)){
            $str .= " and userid != :userid";
            $paras[":userid"] = $execpt;
        }
        $paras[":username"] = $name;
        $paras[":userpass"] = $pass;
        $str .= "; ";
        $num = (new Entity())->Scalar($str, $paras);
        return $num != 0;
    }

    public static function Add($user){
        if(!$user instanceof User) return new Message("对象类型不正确");
        if(User::Exists($user->username, $user->userpass)) return new Message("要添加的用户名或密码已存在");
        $str = "insert into Users (username, userpass, userimg, usertype, usersort, uservalid) "
            ."values (:username, :userpass, :userimg, :usertype, :usersort, :uservalid);";
        $str .= "select userid, username, userimg, usercreatetime, usersort, uservalid from "
            ."Users where userid = @@identity;";
        $paras = array(":username" => $user->username, ":userpass" => $user->userpass, ":userimg" => $user->userimg,
            ":usertype" => $user->usertype, ":usersort" => $user->usersort, ":uservalid" => $user->uservalid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("创建成功111111111", true, new User($en[1][0])) : new Message("创建用户失败");
    }

    public static function Update($user){
        if(!$user instanceof User) return new Message("对象类型不正确");
        if(User::Exists($user->username, $user->userpass, $user->userid)) return new Message("要添加的用户名已存在");
        $str = "update Users set username = :username, userpass = :userpass, userimg = :userimg, usertype = :usertype, "
            ."usersort = :usersort, uservalid = :uservalid where userid = :userid;";
        $str .= "select userid, username, userimg, usertype, usercreatetime, usersort, uservalid "
            ."from Users where userid = :userid; ";
        $paras = array(":userid" => $user->userid, ":username" => $user->username, ":usertype" => $user->usertype,
            ":userpass" => $user->userpass, ":userimg" => $user->userimg, ":usersort" => $user->usersort,
            ":uservalid" => $user->uservalid);
        $en = (new Entity())->Querys($str, $paras);
        return count($en) == 2 && count($en[1]) == 1 ? 
            new Message("修改成功", true, new User($en[1][0])) : new Message("修改失败");
    }

    public static function Add_Update($user){
        if(!$user instanceof User) return new Message("对象类型不正确");
        if($user->userid > 0){
            if($user->userpass == ""){
                $_user = User::getPass($user->userid);
                if(!$_user) return new Message("获取指定用户失败");
                $user->userpass = $_user->userpass; 
            }
        }
        else{
            $user->userpass = User::makePass($user->userpass);
            $user->userimg = $user->userimg == "" ? User::makeImg() : $user->userimg;
        }
        return $user->userid > 0 ? User::Update($user) : User::Add($user);
    }

    public static function Get($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return null;
        $str = "select userid, username, userimg, usertype, usercreatetime, usersort, uservalid "
            ."from Users where userid = :userid; ";
        $paras = array(":userid" => $id);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return null;
        return new User($res);
    }

    public static function Count($search = null){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->name = isset($search->name) ? strval($search->name) : "";
        $search->type = isset($search->type) ? strval($search->type) : "";
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $count = "select count(*) as count ";
        $where = "from Users where 1 = 1 ";
        $paras = array();
        if($search->name != ""){
            $where .= "and ( ";
            $arr = explode(" ", $search->name);
            for($i = 0, $z = count($arr); $i < $z; $i++)
            {
                $where .= "username like concat(\"%\", :str_a_".$i.", \"%\") ";
                if ($i < $z - 1) $where .="or ";
                $paras[":str_a_".$i] = $arr[$i];
            }
            $where .= ") ";
        }
        if($search->type != ""){
            $where .= "and usertype = :usertype ";
            $paras[":usertype"] = $search->type;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and uservalid = :uservalid ";
            $paras[":uservalid"] = $search->valid;
        }
        $count .= $where.";";
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 1 || count($res[0]) != 1) return 0;
        return (int)$res[0][0]["count"];
    }

    public static function GetAll($search = null){
        $search = is_object($search) ? $search : new stdClass(); 
        $search->name = isset($search->name) ? strval($search->name) : "";
        $search->type = isset($search->type) ? strval($search->type) : "";
        $search->valid = isset($search->valid) && is_numeric($search->valid) ? (int)$search->valid : 1;
        $search->page = isset($search->page) && is_numeric($search->page) ? (int)$search->page : 0;
        $search->rows = isset($search->rows) && is_numeric($search->rows) ? (int)$search->rows : 0;
        $count = "select count(*) as count ";
        $select = "select userid, username, userimg, usertype, usercreatetime, usersort, uservalid ";
        $where = "from Users where 1 = 1 ";
        $paras = array();
        if($search->name != ""){
            $where .= "and ( ";
            $arr = explode(" ", $search->name);
            for($i = 0, $z = count($arr); $i < $z; $i++)
            {
                $where .= "username like concat(\"%\", :str_a_".$i.", \"%\") ";
                if ($i < $z - 1) $where .="or ";
                $paras[":str_a_".$i] = $arr[$i];
            }
            $where .= ") ";
        }
        if($search->type != ""){
            $where .= "and usertype = :usertype ";
            $paras[":usertype"] = $search->type;
        }
        if($search->valid == 1 || $search->valid == 0){
            $where .= "and uservalid = :uservalid ";
            $paras[":uservalid"] = $search->valid;
        }
        $count .= $where."; ";
        $where .= "order by usersort desc, userid desc ";
        $select .= $where;
        if($search->page > 0 && $search->rows > 0){
            //$select .= "limit ".($search->page - 1) * $search->rows.", ".$search->rows."; ";
            $select .= "limit :page, :rows; ";
            $paras[":page"] = ($search->page - 1) * $search->rows;
            $paras[":rows"] = $search->rows;
        }
        else $select .= "; ";
        $count .= $select;
        $list = array();
        $res = (new Entity())->Querys($count, $paras);
        if(count($res) != 2 || count($res[0]) != 1) return new Resaults();
        foreach($res[1] as $key => $value) $list[] = new User($value);
        return new Resaults($list, (int)$res[0][0]["count"], $search->page, $search->rows);
    }

    public static function Valid($id, $valid = null){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return new Message("修改失败");
        $valid = is_numeric($valid) ? (int)$valid : null;
        $str = "update Users set ";
        $paras = array();
        if(!$valid){
            $str .= "uservalid = case when uservalid = 0 then 1 else 1 end where userid = :userid; ";
            $paras = array(":userid" => $id);
        }
        else {
            $str .= "uservalid = :uservalid where userid = :userid; ";
            $paras = array(":userid" => $id, ":uservalid" => $valid);
        }
        return (new Entity())->Exec($str, $paras) > 0 ? 
            new Message("修改成功", true) : new Message("修改失败");
    }

    public static function SignIn($pass){
        $str = "select userid, username, userimg, usertype, usercreatetime, usersort, uservalid "
            ."from Users where userpass = :userpass; ";
        $paras = array(":userpass" => $pass);
        $res = (new Entity())->First($str, $paras);
        if(!$res) return new Message("登陆失败");
        return new Message("登陆成功", true, new User($res)); //new User($res);
    }

    public static function SignUp($pass){
        $now = date("H");
        $name = Commen::Rand(3)."#".Commen::Rand(5).".".chr($now + 65).chr($now + 97);
        $arr = array("username" => $name, "userpass" => User::makePass($pass), "userimg" => "images/ac/ac_".rand(1, 50).".png");
        return User::Add(new User($arr));
    }

    public static function MakeUser($pass, $name = null){
        if($name == null) return User::SignUp($pass);
        else return User::SignIn(User::makePass($pass));
    }
}
?>