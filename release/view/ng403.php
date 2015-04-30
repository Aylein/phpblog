<?php
    $err = isset($_GET["err"]) ? $_GET["err"] : "noerr";
    $str = "诶诶 出错了 0 0~";
    switch($err){
        case "nologin": $str = "诶诶 没有登陆吗 ? 0 0~"; break;
    }
?>
            <div class="l_title"><?=$str ?></div>