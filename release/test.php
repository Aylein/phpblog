<?php
include_once("lib/User.php");
/*
$type = Type::Get(12583);
print_r($type);
//$type->typename = "12345";
$arr = Type::Update($type);
print_r($arr);
*/
/*
$type = new Type();
$type->typename = "123456";
print_r(Type::Add($type));
*/
/*
$obj = new stdClass();
$obj->name = "snOter";
print_r(User::GetAll($obj));
*/
//define("EWIORWEJORU", "123");
//echo EWIORWEJORU."<br />";
//$arr = defined("EWIORWEJORU") ? EWIORWEJORU : "123";
//echo $arr;
//die();
?>
<!DOCTYPE html>
<html ng-app="app">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>AyleinOter IV test</title>
        <link rel="stylesheet" href="styles/style.min.css" />
        <link rel="stylesheet" href="styles/index.min.css" />
        <link rel="stylesheet" href="styles/admin.min.css" />
        <link rel="stylesheet" href="styles/extra.min.css" />
    </head>
    <body ng-controller="ngMainController">          
        <div ng-controller="userController">
            <ao-nodes></ao-nodes>
            <div class="l_title">Users</div>
            <div class="l_s">
            </div>
        </div>
    </body>
    <script src="scripts/angular.min.js"></script>
    <script src="scripts/angular-route.min.js"></script>
    <script src="scripts/modules.min.js"></script>
    <script src="scripts/directives.min.js"></script>
    <script src="scripts/controllers.min.js"></script>
</html>