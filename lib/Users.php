<?php
include("Entity.php");
class Users {
    var $userid;
    var $username;
    var $account;
    var $email;
    var $mobile;
    var $password;
    var $createtime;

    public function __construct($array = null){ 
        if($array == null || !is_array($array)) return;
        $this->userid = isset($array["userid"]) && is_numeric($array["userid"]) ? (int)$array["userid"] : 0;
        $this->username = isset($array["username"])  ? $array["username"] : "";
        $this->account = isset($array["account"])  ? $array["account"] : "";
        $this->email = isset($array["email"])  ? $array["email"] : "";
        $this->mobile = isset($array["mobile"])  ? $array["mobile"] : "";
        $this->username = isset($array["username"])  ? $array["username"] : "";
        $this->createtime = isset($array["createtime"])  ? $array["createtime"] : "1900-01-01";
        $this->createtime = strtotime($this->createtime);
        if(!$this->createtime) $this->createtime = strtotime("1900-01-01");
    }

    public function GetUsers(){
        $conn = new Entity();
        $str = "select * from Users;";
        $res = $conn->Query($str);
        if($res !== false) {
            $us = array();
            foreach($res as $ar) $us[] = new Users($ar);
            return $us;
        }
        return false;
    }

    public function GetUserNumber(){
        $conn = new Entity();
        $str = "select count(*) from Users;";
        return $conn->Scalar($str);
    }

    public function GetUser($id){
        if(!is_numeric($id)) return false;
        $conn = new Entity();
        $str = "select * from Users where userid = :userid;";
        $paras = array(":userid" => $id);
        return $conn->First($str, $paras);
    }
}
?>