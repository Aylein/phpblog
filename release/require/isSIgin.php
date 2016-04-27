<?php     
    if(!isset($_SESSION["admin"])){
        header("location:ng403.php?err=nologin");
        die();
    }
?>