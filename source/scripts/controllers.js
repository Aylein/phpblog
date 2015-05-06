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
        web.post("/var/ajax.php", {"action": "getall", "type": "type", "typepid": 0, "show": 1, valid: 1}, function(data){
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
        $scope.sign.types = {};
        web.post("/var/ajax.php", {"action": "getall", "type": "type", "typepid": 0, "show": 1, valid: 1}, function(data){
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
app.controller("typeController", function($scope, main, broswer, cache, web){
    $scope.path = "types";
    $scope.types = {};
    var makeNew = function(){
        return {key: "new", typeid: 0, typepid: 0, typeshow: 0, typename: "", typesort: 0, typevalid: 1};
    };
    var _types;
    $scope.newtype = makeNew();
    var makeType = function(v){
        var s = {
            key: "t_" + v.typeid,
            show: true,
            update: false,
            node: v,
            unode: main.copy(v),
            list: {}
        };
        _types[s.key] = _types[s.key] ? main.extend(_types[s.key], s) : s;
    };
    var makeTypes = function(list){
        _types = _types || {};
        if(main.types._object.test(main.typeof(list))) list = [list];
        main.each(list, function(i, v){ 
            makeType(v);
        });
        $scope.types = {};
        main.each(_types, function(i, v){ 
            v.list = {};
            if(v.node.typepid == 0) $scope.types[v.key] = v; 
        });
        main.each($scope.types, function(i, v){ 
            var f = v;
            main.each(_types, function(i, v){
                if(v.node.typepid == f.node.typeid)
                    f.list[v.key] = v;
            });
        });
    };
    var getTypes = function(callback){
        web.post("/var/admin.php", {_action: "getall", _type: "type"}, callback);
    };
    $scope.show = function(key){
        var s = _types[key] || undefined;
        if(s == undefined) return;
        s.show = s.show ? false : true;
    };
    $scope.showupdate = function(key){
        var tar = key == "new" ? $scope.newtype : _types[key];
        if(key == "new") $scope.newtype = makeNew();
        else tar.update = tar.update ? false : true;
    };
    $scope.update = function(key){
        var tar = key == "new" ? $scope.newtype : _types[key].unode;
        if(tar == undefined) return false;
        var data = main.copy(tar);
        data._action = "post";
        data._type = "type";
        web.post("/var/admin.php", data, function(data){
            if(!data.res) {
                alert(data.msg);
                return;
            }
            makeTypes(data.obj);
            if(key == "new") $scope.newtype = makeNew();
        });
    };
    $scope.drop = function(key){
        var tar = _types[key].unode || undefined;
        if(tar == undefined) return false;
        tar.typevalid = tar.typevalid == 1 ? 0 : 1;
        $scope.update(key);
        /*
        if(tar == undefined) return;
        var data = {_action: "valid", _type: "type", _id: tar.typeid};
        web.post("/var/admin.php", data, function(data){
            if(!data.res) {
                alert(data.msg);
                return;
            }
            makeTypes(data.obj);
        });
        */
    };
    $scope.shown = function(key){
        var tar = key == "new" ? $scope.newtype : _types[key].unode;
        if(tar == undefined) return false;
        tar.typeshow = tar.typeshow == 1 ? 0 : 1;
        if(key == "new") return;
        $scope.update(key);
    };
    $scope.flush = $scope.$parent.flush;
    var init = function(){
        getTypes(function(data){
            data = data.list;
            data.sort(function(a, b){ return a.typesort > b.typesort; });
            makeTypes(data);
            cache.types = _types || {};
        });
    };
    init();
});