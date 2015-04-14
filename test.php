<!DOCTYPE html>
<html>
<head>
    <title>test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <div ng-app="myApp" ng-controller="myCtrl">{{firstName + " " + lastName}}</div>
</body>
<script src="scripts/angular.min.js"></script>
<script src="scripts/jquery-1.11.1.min.js"></script>
<script>
    var app = angular.module("myApp", []);
    app.controller("myCtrl", function($scope){
        $scope.firstName = "John";
        $scope.lastName = "Kennedy";
    });
</script>
</html>