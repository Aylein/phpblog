<?php
    include("../lib/Type.php");
        $type = new Type();
        $type->typeid = isset($_POST["typeid"]) && is_numeric($_POST["typeid"]) ? (int)$_POST["typeid"] : 0;
        $type->typepid = isset($_POST["typepid"]) && is_numeric($_POST["typepid"]) ? (int)$_POST["typepid"] : 0;
        $type->typeshow = isset($_POST["typeshow"]) && is_numeric($_POST["typeshow"]) ? (int)$_POST["typeshow"] : 0;
        $type->typename = isset($_POST["typename"]) ? $_POST["typename"] : "";
        $type->typesort = isset($_POST["typesort"]) && is_numeric($_POST["typesort"]) ? (int)$_POST["typesort"] : 0;
        $type->typevalid = isset($_POST["typevalid"]) && is_numeric($_POST["typevalid"]) ? (int)$_POST["typevalid"] : 0;
        $res;
        if($type->typeid != 0) $res = Type::Update($type);
        else $res = Type::Add($type);
        print_r($res);
?>