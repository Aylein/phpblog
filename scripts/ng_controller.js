var app = angular.module("app");
app.controller("ngMainController", function($scope, $rootScope, $location, $route, web){
    $scope.clearCur = function(){
        $rootScope.sign.all.cur = 0;
        $rootScope.sign.say.cur = 0;
        $rootScope.sign.about.cur = 0;
        for(var i in $rootScope.sign.types)
            $rootScope.sign.types[i].cur = 0;
    };
    $scope.click = function(t_id){
        $scope.clearCur();
        switch(t_id){
            case "all": $location.path("/index"); break;
            case "say": $location.path("/says"); break;
            case "about": $location.path("/about"); break;
            default:
                if($rootScope.sign.types["t_" + t_id])
                    $location.path("/found/" + t_id);
                break;
        };
    };
    $scope.makeCur = function(){
        $scope.clearCur();
        var path = $location.path(), typeid = $route.current.params.typeid;
        if(path.match(/^\/found\/\d+$/)) {
            if($rootScope.sign.types["t_" + typeid])
                $rootScope.sign.types["t_" + typeid].cur = 1;
        }
        else if(path == "/index") $rootScope.sign.all.cur = 1;
        else if(path == "/says") $rootScope.sign.say.cur = 1;
        else if(path == "/about") $rootScope.sign.about.cur = 1;
    };
    var init = function(){
        $rootScope.sign.types = {};
        $rootScope.sign.sign = "What a loser";
        $rootScope.sign.himg = "/images/headerimg.jpg";
        $rootScope.sign.all = {cur: 0, typeid: "all", typename: "全部"};
        $rootScope.sign.say = {cur: 0, typeid: "say", typename: "Says"};
        $rootScope.sign.about = {cur: 0, typeid: "about", typename: "About"};
        web.post("/var/types.php", {"action": "gettypes", "typepid": 0}, function(data){
            if(data.err == false) return;
            for(var i = 0, z = data.list.length; i < z; i++){
                data.list[i].cur = 0;
                $rootScope.sign.types["t_" + data.list[i].typeid] = data.list[i];
            }
            $scope.makeCur("all");
            $scope.$on("$locationChangeSuccess", $scope.makeCur);
        });
    };
    if($rootScope.sign) return;
    $rootScope.sign = {};
    init();
});
app.controller("urlController", function($scope, web, $location, $routeParams){
    var path = $location.path(), typeid = $routeParams.typeid;
});