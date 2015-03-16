<?php
include_once("Entity.php");
include_once("Main.php");
class User {
    var $userid;
    var $username;
    var $userpass;
    var $userimg;
    var $userreatetime;
    var $usersort;
    var $uservalid;

    public function __construct($array = null){
        if($array == null || !is_array($array)) return;
        $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
        $this->username = isset($array["username"])  ? $array["username"] : "";
        $this->userpass = isset($array["userpass"])  ? $array["userpass"] : "";
        $this->userimg = isset($array["userimg"])  ? $array["userimg"] : "";
        $this->userreatetime = isset($array["userreatetime"])  ? $array["userreatetime"] : "";
        $this->usersort = isset($array["usersort"]) && is_numeric($array["usersort"]) ? (int)$array["usersort"] : 0;
        $this->uservalid = isset($array["uservalid"]) && is_numeric($array["uservalid"]) ? (int)$array["uservalid"] : 1;
    }

    public static function Add($user){
        if(!is_a($user, "User")) return false;
        $str = "insert into Users (username, userpass, userimg, usersort, uservalid) "
            ."values (:username, :userpass, :userimg, :usersort, :uservalid);";
        $str .= "select userid, username, userimg, usercreatetime, usersort, uservalid from "
            ."Users where userid = @@identity;";
        $paras = array(":username" => $user->username, ":userpass" => $user->userpass, ":userimg" => $user->userimg,
            ":usersort" => $user->usersort, ":uservalid" => $user->uservalid);
        $en = new Entity();
        $obj = $en->Querys($str, $paras);
        if(count($obj[1]) != 1) return new Message();
        return new Message(true, "ok", "ok", new User($obj[1][0]));
    }

    public static function Update($user){
        if(!is_a($user, "User")) return false;
        $str = "update Users set username = :username, userpass = :userpass, userimg = :userimg, "
            ."usersort = :usersort, uservalid = :uservalid where userid = :userid;";
        $paras = array(":userid" => $user->userid, ":username" => $user->username,
            ":userpass" => $user->userpass, ":userimg" => $user->userimg, ":usersort" => $user->usersort,
            ":uservalid" => $user->uservalid);
        return $en->Exec($str, $paras);
    }

    public static function Get($id){
        $id = is_int($id) ? $id : 0;
        if($id < 1) return false;
        $str = "select userid, username, userimg, usercreatetime, usersort, uservalid "
            ."from Users where userid = :userid; ";
        $paras = array(":userid" => $id);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return false;
        return new User($res);
    }

    public static function SignIn($pass){
        $str = "select userid, username, userimg, usercreatetime, usersort, uservalid "
            ."from Users where userpass = :userpass; ";
        $paras = array(":userpass" => $pass);
        $en = new Entity();
        $res = $en->First($str, $paras);
        if(!$res) return new Message();
        return new Message(true, "ok", "ok", new User($res)); //new User($res);
    }

    public static function SignUp($pass){
        $now = date("H");
        $name = Commen::Rand(3)."#".Commen::Rand(5).".".chr($now + 65).chr($now + 97);
        $arr = array("username" => $name, "userpass" => md5($name." ".$pass), "userimg" => "images/ac/ac_".rand(1, 50).".jpg");
        return User::Add(new User($arr));
    }

    public static function MakeUser($pass, $name = null){
        if($name == null) return User::SignUp($pass);
        else return User::SignIn(md5($name." ".$pass));
    }
}
?>