/* global angular */
var app = angular.module("app");
app.controller("ngMainController", function($scope, $location, $route, web, cache, cv){
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
        if(path.match(/^\/found\/\d+$/)){
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
        cache.set("cv", cv.init({
            cover: {clsname: "apt", style: {position: "fixed", top: "0", left: "0", right: "0", bottom: "0", background: "#ff00ff"}},
            dialog: {clsname: "col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4", style: {"top": "24px"}},
            title: {text: "title"},
            content: {text: "message", style: {"min-height": "40px", "font-size": "18px", "line-height": "40px"}},
            close: {click: function(){ if(!close || close(this) !== false) this.hide(); }, ppp: "ppppp", style: {key: "key", key1: "key1"}},
            button: [{
                key: "yes",
                clsname: "btn btn-primary",
                html: "确定",
                click: function(){ if(!callback || callback(this) !== false) this.hide(); }
            }]
        }));
        var cvm = cache.get("cv");
        cvm.show();
        console.log(cache.all(), cvm);
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
app.controller("saysController", function($scope, main, cache, web){
    var init = function(){
        $scope = $scope || {};
        $scope.comment = {comid: 0, comment: "", show: true, showHide: false};        
        web.post("/var/ajax.php", {
            action: "getall", 
            type: "comment", 
            "deep": true
        }, function(data){
            $scope.list = data.list;
        });
        console.log($scope);
    };
    init();
});
app.controller("adminController", function($scope, main, cache){
    $scope.path = "admins";
});
app.controller("typeController", function($scope, main, cache, web){
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
        if(tar.typename == ""){
            alert("请填写分类名称");
            return false;
        }
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
app.controller("stageController", function($scope, main, cache, web){
    $scope.path = "stages";
});
app.controller("userController", function($scope, main, cache, web){
    $scope.path = "users";
    $scope.new = {
        userid: 0,
        username: "", 
        userpass: "", 
        userimg: "", 
        usertype: "visit", 
        usercreatetime: "", 
        userlastaction: "", 
        usersort: 0, 
        uservalid: 1
    };
    var makeList = function(list){
        $scope.list = $scope.list || {};
        main.each(list, function(i, v){
            $scope.list["u_" + v.userid] = {n: v, u: main.copy(v)};
        });
    };
    var init = function(){
        var data = [
            {
                userid: 1, 
                username: "sYs#tsdfet.Aa", 
                userpass: "safsafsdfsdfsfsafsafsdafsa", 
                userimg: "/images/ac/ac_1.png", 
                usertype: "visit", 
                usercreatetime: "2014-01-01 00:00:00", 
                userlastaction: "2014-01-01 00:00:00", 
                usersort: 0, 
                uservalid: 1
            },{
                userid: 2, 
                username: "sYs#sceedf.Aa", 
                userpass: "asdfasfsdafsdafeaearwerqrew", 
                userimg: "/images/ac/ac_2.png", 
                usertype: "visit", 
                usercreatetime: "2014-01-01 00:00:00", 
                userlastaction: "2014-01-01 00:00:00", 
                usersort: 0, 
                uservalid: 1
            },{
                userid: 3, 
                username: "sYs#teftgd.Aa", 
                userpass: "twetrgsffewrwetregdgfswfaewr", 
                userimg: "/images/ac/ac_3.png", 
                usertype: "visit", 
                usercreatetime: "2014-01-01 00:00:00", 
                userlastaction: "2014-01-01 00:00:00", 
                usersort: 0, 
                uservalid: 1
            },{
                userid: 4, 
                username: "sYs#ewdcge.Aa", 
                userpass: "fgewtwerewrdsfewrwefggerewr", 
                userimg: "/images/ac/ac_4.png", 
                usertype: "visit", 
                usercreatetime: "2014-01-01 00:00:00", 
                userlastaction: "2014-01-01 00:00:00", 
                usersort: 0, 
                uservalid: 1
            },{
                userid: 5, 
                username: "sYs#ertgfc.Aa", 
                userpass: "hdfshrhrtwewrwefewr", 
                userimg: "/images/ac/ac_5.png", 
                usertype: "visit", 
                usercreatetime: "2014-01-01 00:00:00", 
                userlastaction: "2014-01-01 00:00:00", 
                usersort: 0, 
                uservalid: 1
            }
        ];
        makeList(data);
    };
    init();
});