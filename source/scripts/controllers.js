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
    $scope.flush = function(){
        web.post("/var/ajax.php", {"action": "getall", "type": "type", "typepid": 0, "show": 1}, function(data){
            if(data.err == false) return;
            for(var i = 0, z = data.list.length; i < z; i++){
                data.list[i].cur = 0;
                $scope.sign.types["t_" + data.list[i].typeid] = data.list[i];
            }
            $scope.makeCur();
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
app.controller("adminController", function($scope, main, broswer, cache){
    $scope.path = "admins";
});
app.controller("typeController", function($scope, main, broswer, cache){
    $scope.path = "types";
    $scope.types = {};
    var _types;
    var makeTypes = function(list){
        _types = _types || {};
        main.each(list, function(i, v){
            v.key = "t_" + v.typeid;
            v.show = 1;
            _types[v.key] = v;
            if(v.typepid == 0){ 
                v.list = v.list || {};
                $scope.types[v.key] = v;
            }
        });
        cache.types = _types || {};
    };
    var makeList = function(){
        main.each(_types, function(i, k, v){
            if(v.typepid != 0){
                var s = _types["t_" + v.typepid];
                s.list = s.list || [];
                s.list[v.key] = v;
            }
        });
    };
    var init = function(){
        var data = [
            {typeid: 1, typepid: 0, typeshow: 0, typename: "all1", typesort: 0, typevalid: 1},
            {typeid: 2, typepid: 1, typeshow: 0, typename: "all2", typesort: 0, typevalid: 1},
            {typeid: 3, typepid: 1, typeshow: 0, typename: "all3", typesort: 0, typevalid: 1},
            {typeid: 4, typepid: 0, typeshow: 0, typename: "all4", typesort: 0, typevalid: 1},
            {typeid: 5, typepid: 4, typeshow: 0, typename: "all5", typesort: 0, typevalid: 1},
            {typeid: 6, typepid: 4, typeshow: 0, typename: "all6", typesort: 0, typevalid: 1},
            {typeid: 7, typepid: 0, typeshow: 0, typename: "all7", typesort: 0, typevalid: 1},
            {typeid: 8, typepid: 7, typeshow: 0, typename: "all8", typesort: 0, typevalid: 1},
            {typeid: 9, typepid: 7, typeshow: 0, typename: "all9", typesort: 0, typevalid: 1}
        ];
        data.sort(function(a, b){ return a.typesort > b.typesort; });
        makeTypes(data);
        makeList();
        _types["t_2"].name = "name";
    };
    $scope.show = function(key){
        var s = _types[key] || undefined;
        if(s == undefined) return;
        s.show = s.show == 1 ? 0 : 1;
    };
    init();
});