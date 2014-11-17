<?php
    include("lib/type.php");
    $typeid = isset($_GET["type"]) && is_numeric($_GET["type"]) ? (int)$_GET["type"] : 0;
    $type = new Type();
    if(isset($type->MakeJson)) echo "yes";
    else echo "no";
    die();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Index</title>
        <link rel="stylesheet" href="styles/style.css" />
        <link rel="stylesheet" href="styles/index.css" />
    </head>
    <body>
        <section class="header"><?php require("require/header.php"); ?></section>
        <section class="topimg"></section>
        <section class="bodypano">
            <div class="pano_left">
            </div>
            <div class="pano_right">
                <div class="item namespace">
                    <div class="imgspace"><img src="/images/master.jpg"/></div>
                    <div class="name">
                        <div class="bb1s666">AyleinOter IV</div>
                        <div class="sign">What a loser</div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>
            <br />
        </section>
        <section class="footer"><?php require("require/footer.php"); ?></section>
    </body>
</html>