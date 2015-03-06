<?php
include_once("../lib/type.php");
include_once("../lib/main.php");
class serverTypes{
    public function auth($a)
    {
        if($a != "123456789")
            throw new SoapFault("Server", "您无权访问");
    }
    public function postType($type){
        //if(!($type instanceof Type)) return new Message();
        return new Message(true, "ok", "", $type);
    }
    public function putType($type){
        //if(!($type instanceof Type)) return new Message();
        return new Message(true, "ok", "", $type);
    }
    public function deleteType($id){
        if(!is_int($id) || $id < 1) return new Message();
        return new Type();
    }
    public function getTypes($typepid = -1, $show = -1, $valid = -1, $pagenum = 1, $pagesize = 0){
        return Type::GetTypes($typepid, $show, $valid, $pagenum, $pagesize);
    }
    public function getType($id){
        if(!is_int($id) || $id < 1) return new Message();
        return new Type();
    }
    public function Hi($s){
        return $s;
    }
}
$s = new SoapServer(null, array("location"=>"http://localhost:82/server/types.php","uri"=>"types.php"));
$s->setClass("serverTypes");
$s->handle();
?>