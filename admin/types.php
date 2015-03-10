<?php
    include("../lib/Type.php");
    $atype = Type::GetTypes(0);
    $ctype = Type::GetTypes(-2);
    $counta = count($atype->list);
    $countc = count($ctype->list);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>admin types</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/admin_style.css" />
    <link rel="stylesheet" href="/styles/extra.css" />
</head>
<body>
    <section class="header"><?php require("../require/header.php"); ?></section>
    <section class="topimg"><?php require("../require/headimg.php"); ?></section>
    <section class="bodypano">
        <div class="left_hand"><?php require("../require/admin_menu.php"); ?></div>
        <div class="right_hand" ng-app="app" ng-controller="typesController">
            <div class="rh_title" id="typelist">所有类别</div>
            <div class="rh_item_c" ng-show="types.length < 1">目前尚未有类型</div>
            <div ng-show="types.length > 0">
                <div ng-repeat="type in types" class="rh_item {{type.typevalid == 1 ? 'c666' : 'cCCC'}}">
                    <a href="/admin/newtype.php?id={{type.typeid}}" class="{{type.typevalid == 1 ? 'c666' : 'cCCC'}}" ng-bind="type.typename + ' / '"></a>
                    <span ng-bind="type.typeid + ' / '"></span>
                    <span ng-bind="type.typeshow == 1 ? 'show' : 'hide'"></span>
                    <div ng-repeat="child in children['c_' + type.typeid]" class="rh_item ml25 {{child.typevalid == 1 ? 'c666' : 'cCCC'}}">
                        <a href="/admin/newtype.php?id={{child.typeid}}" class="{{child.typevalid == 1 ? 'c666' : 'cCCC'}}" ng-bind="child.typename + ' / '"></a>
                        <span ng-bind="child.typeid + ' / '"></span>
                        <span ng-bind="child.typeshow == 1 ? 'show' : 'hide'"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <br />
    </section>
    <section class="footer"><?php require("../require/footer.php"); ?></section>
</body>
<script src="../scripts/jquery-1.11.1.min.js"></script>
<script src="../scripts/angular.min.js"></script>
<script src="../scripts/ag_module.js"></script>
<script>
    var app = angular.module("app");
    app.controller("typesController", function($scope, web){
        $scope.children = {};
        web.post("/var/types.php", {"action": "gettypes", "typepid": 0}, function(data){
            if(data.err == false) return;
            $scope.types = data.list;
            for(var i = 0, z = data.list.length; i < z; i++){
                (function(i, f){
                    web.post("/var/types.php", {"action": "gettypes", "typepid": f.typeid}, function(data){
                        if(data.err == false) return;
                        $scope.children["c_" + f.typeid] = data.list;
                        console.log($scope.children);
                    });
                })(i, data.list[i]);
            }
        });
    });
</script>
</html>