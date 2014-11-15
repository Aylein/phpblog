<?php
    //require("../require/state.php");
    include("../lib/Entity.php");
    //$list = Type::GetType
    $str = "select count(*) as count from Types;";
    $str .= "select * from Types;";
    $en = new Entity();
    $res = $en->Query($str);
    print_r($res);
    die();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>admin types</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/admin_style.css" />
</head>
<body>
    <section class="header"><?php require("../require/header.php"); ?></section>
    <section class="topimg"></section>
    <section class="bodypano">
        <div class="left_hand"><?php require("../require/admin_menu.php"); ?></div>
        <div class="right_hand">
            <div class="list_title">
                <div class="list_item"></div>
            </div>
            <div class="list" id="list">
                
            </div>
        </div>
        <div class="clear"></div>
        <br />
    </section>
    <section class="footer"><?php require("../require/footer.php"); ?></section>
</body>
</html>