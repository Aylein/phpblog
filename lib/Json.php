<?php
class Json{
    public static function MakeJson($bo = true, $code = "ok", $msg = "操作成功", $ex = ""){
        $bo = is_bool($bo) ? $bo : false;
        $code = is_string($code) ? $code : "err";
        $msg = is_string($msg) ? $msg : "操作失败";
        $ex = is_string($ex) ? $ex : "";
        $str = "{ ";
        if($bo) $str .= "\"ok\": \"".$code."\", ";
        else $str .= "\"err\": \"".$code."\", ";
        $str .= "\"msg\": \"".$msg."\"";
        if($ex != "") $str .= " ".$ex." ";
        else $str .= " ";
        $str .= "}";
        return $str;
    }
}
?>