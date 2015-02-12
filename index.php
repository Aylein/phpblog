<?php
    include("lib/type.php");
    include("lib/Document.php");
    $typeid = isset($_GET["type"]) && is_numeric($_GET["type"]) ? (int)$_GET["type"] : 0;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Index</title>
        <link rel="stylesheet" href="styles/style.css" />
        <link rel="stylesheet" href="styles/index.css" />
        <link rel="stylesheet" href="styles/extra.css" />
    </head>
    <body>
        <section class="header"><?php require("require/header.php"); ?></section>
        <section class="topimg"><img src="/images/headerimg.jpg"/></section>
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
                <div class="item namespace mt5">
                    <div><audio controls="controls" autoplay="autoplay" src="sounds/白いスーツのテーマ.mp3">您的浏览器不支持 audio 标签。</audio></div>
                </div>
            </div>
            <div class="clear"></div>
            <br />
        </section>
        <section class="footer"><?php require("require/footer.php"); ?></section>
    </body>
    <script src="scripts/oter.js"></script>
</html>