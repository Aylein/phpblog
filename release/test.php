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
            <ao-pg model="pager"></ao-pg>
            <ao-nodes></ao-nodes>
            <div class="l_title">Users</div>
            <div class="l_s">
                <div>
                    <div>展开 添加新会员</div>
                    <div class="l_new">
                        <div class="l_new_img" style="background: url(./images/ac/ac_13.png) no-repeat 100%; background-size: 100%"></div>
                        <div class="l_new_info">
                            <form name="new_user" ng-submit="newTest.submit()">
                                <input type="text" ng-class="{'bA50000': newTest.usernameRes == 'error'}" name="username" ng-model="new.username" ng-blur="newTest.usernameTest()">
                                <span class="cA50000" ng-bind="newTest.usernameText"></span><br>
                                <input type="password" ng-class="{'bA50000': newTest.userpassRes == 'error'}" name="userpass" ng-model="new.userpass" ng-blur="newTest.userpassTest()">
                                <span class="cA50000" ng-bind="newTest.userpassText"></span><br>
                                <select name="usertype" ng-model="new.usertype" ng-options="type as type for type in newTest.usertypes"></select>
                                <input type="submit" value="确定">
                                <input type="reset" ng-click="newTest.reset()" value="重置">
                            </form>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <br>
                <div class="list">
                    <div ng-repeat="item in list" class="mt15">
                        <div class="l_new_img" style="background: url({{item.n.userimg}}) no-repeat 100%; background-size: 100%"></div>
                        <div class="l_new_info">
                            <span ng-bind="item.n.username"></span> - <span ng-bind="item.n.usercreatetime"></span> ~ <span ng-bind="item.n.userlastaction"></span>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="scripts/angular.min.js"></script>
    <script src="scripts/angular-route.min.js"></script>
    <script src="scripts/modules.min.js"></script>
    <script src="scripts/directives.min.js"></script>
    <script src="scripts/controllers.min.js"></script>
</html>