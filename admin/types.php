<?php
    include("../lib/Type.php");
    $atype = Type::GetTypes(0);
    $ctype = Type::GetTypes(-2);
    $counta = count($atype->list);
    $countc = count($ctype->list);

    $s = new SoapClient(null, array("location"=>"http://localhost:82/server/types.php","uri"=>"types.php"));
    $h = new SoapHeader("http://localhost:82/server/types.php", "auth", "123456789", false, SOAP_ACTOR_NEXT);
    $s->__setSoapHeaders(array($h));
    //print_r(json_encode($s->postType(new Type())));
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
        <div class="right_hand" ng-app="asp" ng-controller="asptta">
            <div class="rh_title" id="typelist">所有类别</div>
            <div class="rh_item_c" ng-show="types.length < 1">目前尚未有类型</div>
            <div ng-show="types.length > 0">
                <div ng-repeat="type in types" class="rh_item {{type.typevalid == 1 ? 'c666' : 'cCCC'}}">
                    <a href="/admin/newtype.php?id={{type.typeid}}" class="{{type.typevalid == 1 ? 'c666' : 'cCCC'}}" ng-bind="type.typename"></a> /
                    <span ng-bind="type.typeid"></span> /
                    <span ng-bind="type.typeshow == 1 ? 'show' : 'hide'"></span>
                </div>
            </div>
            <!--div>
                <?php if($counta > 0): ?>
                <?php for($i = 0; $i < $counta; $i++): ?>
                <div class="rh_item <?=$atype->list[$i]->typevalid == 1 ? "c666" : "cCCC" ?>">
                    <a href="/admin/newtype.php?id=<?=$atype->list[$i]->typeid ?>" class="<?=$atype->list[$i]->typevalid == 1 ? "c666" : "cCCC" ?>"><?=$atype->list[$i]->typename ?></a> /
                    <span><?=$atype->list[$i]->typeid ?></span> /
                    <span><?=$atype->list[$i]->typeshow == 1 ? "show" : "hide" ?></span>
                </div>
                <?php for($j = 0; $j < $countc; $j++): ?>
                <?php if($atype->list[$i]->typeid == $ctype->list[$j]->typepid): ?>
                <div class="rh_item_c  <?=$ctype->list[$j]->typevalid == 1 ? "c666" : "cCCC"?>">
                    <a href="/admin/newtype.php?id=<?=$ctype->list[$j]->typeid ?>"  class="<?=$ctype->list[$j]->typevalid == 1 ? "c666" : "cCCC" ?>"><?=$ctype->list[$j]->typename ?></a> /
                    <span><?=$ctype->list[$j]->typeid ?></span> /
                    <span><?=$ctype->list[$j]->typeshow == 1 ? "show" : "hide" ?></span>
                </div>
                <?php endif; ?>
                <?php endfor; ?>
                <?php endfor; ?>
                <?php else: ?>
                <div class="rh_item_c">目前尚未有类型</div>
                <?php endif; ?>
            </div-->
        </div>
        <div class="clear"></div>
        <br />
    </section>
    <section class="footer"><?php require("../require/footer.php"); ?></section>
</body>
<script src="../scripts/jquery-1.11.1.min.js"></script>
<script src="../scripts/angular.min.js"></script>
<script src="../scripts/params.js"></script>
<script>
    var asp = angular.module("asp", []);
    asp.controller("asptta", function($scope, $http){
        $http({
            method: "post",
            url: "/var/types.php",
            data: {"action": "gettypes"},
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            transformRequest: Param
        }).success(function(data){
            console.log(data);
            if(data.err == false) return;
            $scope.types = data.list;
        });
    });
</script>
</html>