<?php
/*
include_once("./lib/User.php");
echo User::makePass("mm19880209");
die();
*/
?>


<!DOCTYPE html>
<html ng-app="app">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" type="image/ico" href="/favicon.ico">
        <title>AyleinOter IV test</title>
        <link rel="stylesheet" href="styles/style.min.css" />
        <link rel="stylesheet" href="styles/index.min.css" />
        <link rel="stylesheet" href="styles/admin.min.css" />
        <link rel="stylesheet" href="styles/extra.min.css" />
    </head>
    <body>          
        <div ng-controller="saysController">
            <div class="l_title">This is the Says page</div>
            <div class="says" style="margin-top: 15px;">
                <div class="says_left bc666"></div>
                <div class="says_right">
                    <div>
                        <div class="say_title">填充 :</div>
                        <div class="say_item"><ao-cs model="comment" /></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <br />
            <ao-rp ng-repeat="com in list" model="com"></ao-rp>
            <ao-pg model="pager"></ao-pg>
        </div>
    </body>
    <script src="scripts/angular.min.js"></script>
    <script src="scripts/angular-route.min.js"></script>
    <script src="scripts/modules.min.js"></script>
    <script src="scripts/directives.min.js"></script>
    <script src="scripts/controllers.min.js"></script>
</html>