<?php
    include_once("../lib/Comment.php");

    if(!isset($_POST["action"])){
        echo Json::MakeJson(false, "no_action", "你想干什么 0 0~");
        die();
    }
    $action = $_POST["action"];
    $str = "";
    switch($action){
        case "newcom":
            $str = NewComment();
            break;
        case "getcomms":
            $str = GetComments();
            break;
        default: $str = Json::MakeJson(false, "no_action", "你想干什么 0 0~"); break;
    }
    echo $str;

    private function NewComment(){
        return Json::MakeJson(false);
    }
    
    private function GetComments(){
        return Json::MakeJson(false);
    }
?>