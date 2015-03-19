<?php
    Session_start();
    if(!isset($_SESSION["admin"])) {
        echo "no session";
        header("location: /signin.html");
        exit();
    }
    echo "yes";
?>