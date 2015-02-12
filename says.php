<?php
    include("lib/type.php");
    include("lib/Comment.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>What they say</title>
        <link rel="stylesheet" href="styles/style.css" />
        <link rel="stylesheet" href="styles/index.css" />
        <link rel="stylesheet" href="styles/extra.css" />
    </head>
    <body>
        <section class="header"><?php require("require/header.php"); ?></section>
        <section class="topimg"><img src="/images/headerimg.jpg"/></section>
        <section class="bodypano">
            <div class="pano_left">
                <div class="says h80"></div>
                <div class="says bb1s666">Syas:</div>
                <div class="says" style="margin-top: 15px;">
                    <div class="says_left bc666"></div>
                    <div class="says_right">
                        <div>
                            <div class="say_title">填充 :</div>
                            <div class="say_item">
                                <div class="say_button">
                                    <div class="say_button_item" id="bold_type" cmd="bold">加粗</div>
                                </div>
                                <div class="say_comment" id="content" contenteditable="true"></div>
                                <div>
                                    <input type="submit" value="Send" class="h30 mt9 w80"/>
                                    <input type="text" class="h24 mt9 w80"/>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
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
    <script src="scripts/content.js"></script>
</html>