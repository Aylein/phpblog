var app = angular.module("app");
app.controller("ngMainController", function($scope, $location, $route, web, cache){
    $scope.clearCur = function(){
        $scope.sign.all.cur = 0;
        $scope.sign.say.cur = 0;
        $scope.sign.about.cur = 0;
        for(var i in $scope.sign.types)
            $scope.sign.types[i].cur = 0;
    };
    $scope.click = function(t_id){
        $scope.clearCur();
        switch(t_id){
            case "all": $location.path("/index"); break;
            case "say": $location.path("/says"); break;
            case "about": $location.path("/about"); break;
            default:
                if($scope.sign.types["t_" + t_id])
                    $location.path("/found/" + t_id);
                break;
        };
    };
    $scope.makeCur = function(){
        $scope.clearCur();
        var path = $location.path(), typeid = $route.current.params.typeid;
        if(path.match(/^\/found\/\d+$/)) {
            if($scope.sign.types["t_" + typeid])
                $scope.sign.types["t_" + typeid].cur = 1;
        }
        else if(path == "/index") $scope.sign.all.cur = 1;
        else if(path == "/says") $scope.sign.say.cur = 1;
        else if(path == "/about") $scope.sign.about.cur = 1;
    };
    var init = function(){
        $scope.sign.types = {};
        $scope.sign.sign = "What a loser";
        $scope.sign.himg = "/images/headerimg.jpg";
        $scope.sign.all = {cur: 0, typeid: "all", typename: "全部"};
        $scope.sign.say = {cur: 0, typeid: "say", typename: "Says"};
        $scope.sign.about = {cur: 0, typeid: "about", typename: "About"};
        web.post("/var/ajax.php", {"action": "getall", "type": "type", "typepid": 0, "show": 1}, function(data){
            if(data.err == false) return;
            for(var i = 0, z = data.list.length; i < z; i++){
                data.list[i].cur = 0;
                $scope.sign.types["t_" + data.list[i].typeid] = data.list[i];
            }
            $scope.makeCur("all");
            $scope.$on("$locationChangeSuccess", $scope.makeCur);
        });
    };
    $scope.sign = cache.sign = {};
    init();
});
app.controller("urlController", function($scope, web, $location, $routeParams){
    var path = $location.path(), typeid = $routeParams.typeid;
});
app.controller("saysController", function($scope, main, broswer, cache){
});