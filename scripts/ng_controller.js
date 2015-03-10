var app = angular.module("app");
app.controller("ngMainController", function($scope, web, $location, $route){
    $scope.types = {};
    $scope.sign = "What a loser";
    $scope.himg = "/images/headerimg.jpg";
    $scope.all = {cur: 0, typeid: "all", typename: "全部"};
    $scope.say = {cur: 0, typeid: "say", typename: "Says"};
    $scope.clearCur = function(){
        $scope.all.cur = 0;
        $scope.say.cur = 0;
        for(var i in $scope.types)
            $scope.types[i].cur = 0;
    };
    $scope.click = function(t_id){
        $scope.clearCur();
        switch(t_id){
            case "all": $location.path("/index"); break;
            case "say": $location.path("/says"); break;
            default:
                if($scope.types["t_" + t_id])
                    $location.path("/found" + t_id);
                break;
        };
    };
    $scope.makeCur = function(){
        $scope.clearCur();
        var path = $location.path(), typeid = $route.current.params.typeid;
        if(path.match(/^\/found\d+$/)) {
            if($scope.types["t_" + typeid])
                $scope.types["t_" + typeid].cur = 1;
        }
        else if(path == "/index") $scope.all.cur = 1;
        else if(path == "/says") $scope.say.cur = 1;
    };
    var init = function(){
        web.post("/var/types.php", {"action": "gettypes", "typepid": 0}, function(data){
            if(data.err == false) return;
            for(var i = 0, z = data.list.length; i < z; i++){
                data.list[i].cur = 0;
                $scope.types["t_" + data.list[i].typeid] = data.list[i];
            }
            $scope.makeCur("all");
            $scope.$on("$locationChangeSuccess", $scope.makeCur);
        });
    };
    init();
});
app.controller("urlController", function($scope, web, $location, $routeParams){
    var path = $location.path(), typeid = $routeParams.typeid;
});