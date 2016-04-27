"use strict";
/* global angular */
var app = angular.module("app");
app.controller("ngMainController", function($scope, $location, $route, web, cache, cookie, cv, debug){
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
        $scope.cv = cv;
        $scope.debug = debug;
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
        $scope.send = function(model){
            console.log(model);
        };  
        web.post("/var/ajax.php", {
            action: "getall", 
            type: "comment", 
            "deep": true
        }, function(data){
            $scope.list = data.list;
        });
    };
    init();
});
app.controller("adminController", function($scope, main, cache){
    $scope.path = "admins";
});
app.controller("adminTypeController", function($scope, main, cache, web){
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
            $scope.debug("warnning", "请填写分类名称");
            return false;
        }
        var data = main.copy(tar);
        data._action = "post";
        data._type = "type";
        web.post("/var/admin.php", data, function(data){
            if(!data.res) {
                $scope.debug("error", data.msg);
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
app.controller("adminUserController", function($scope, main, extra, cache, web, debug, cv, $routeParams){
    $scope.path = "users";
    $scope.pager = {
        page: 0, 
        total: 0, 
        show: 10, 
        callback: function(page, event){
            init(page);
            event.preventDefault();
        }
    };
    $scope.newTest = {
        usertypes: [],
        uservalid: [{val: 1, text: "可用"}, {val: 0, text: "不可用"}],
        usernameRes: "init",
        usernameText: "",
        usernameTest: function(){
            // var va = main.trim($scope.new.username);
            // if(va.length < 1){
            //     this.usernameRes = "error";
            //     this.usernameText = "登录名不能为空";
            // }
            // else if(main.charLength(va) < 6){
            //     this.usernameRes = "error";
            //     this.usernameText = "登录名不能小于6位";
            // }
            // else if(main.charLength(va) > 25){
            //     this.usernameRes = "error";
            //     this.usernameText = "登录名不能大于18位";
            // }
            // else{
                this.usernameRes = "ok";
                this.usernameText = ""; 
            // }
        },
        userpassRes: "init",
        userpassText: "",
        userpassTest: function(){
            var va = main.trim($scope.new.userpass);
            if(va.length < 1){
                this.userpassRes = "error";
                this.userpassText = "登录密码不能为空";
            }
            else if(main.charLength(va) < 6){
                this.userpassRes = "error";
                this.userpassText = "登录密码不能小于6位";
            }
            else if(main.charLength(va) > 25){
                this.userpassRes = "error";
                this.userpassText = "登录密码不能大于25位";
            }
            else{
                this.userpassRes = "ok";
                this.userpassText = ""; 
            }
        },
        submit: function(){
            if(this.usernameRes == "init") this.usernameTest();
            if(this.userpassRes == "init") this.userpassTest();
            if(this.usernameRes == "ok" && this.userpassRes == "ok"){
                var data = main.copy($scope.new);
                data._action = "post";
                data._type = "user";
                web.post("var/admin.php", data, function(data){
                    if(data.res){
                        debug.success("添加新用户成功");
                        $scope.newTest.resetNew();
                        $scope.newTest.reset();
                        init(1);
                    }
                    else debug.error("添加新用户失败：" + data.msg);
                });
            }
            return false;
        },
        reset: function(){
            this.usernameRes = "init";
            this.usernameText = "";
            this.userpassRes = "init";
            this.userpassText = "";
        },
        resetNew: function(){
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
        }
    }
    $scope.updateTest = {
        updateid: 0,
        usernameRes: "init",
        usernameText: "",
        usernameTest: function(key){
            var va = main.trim($scope.list[key].u.username);
            if(va.length < 1){
                this.usernameRes = "ok";
                this.usernameText = ""; 
            }
            else if(main.charLength(va) < 6){
                this.usernameRes = "error";
                this.usernameText = "登录名不能小于6位";
            }
            else if(main.charLength(va) > 25){
                this.usernameRes = "error";
                this.usernameText = "登录名不能大于18位";
            }
            else{
                this.usernameRes = "ok";
                this.usernameText = ""; 
            }
        },
        userpassRes: "init",
        userpassText: "",
        userpassTest: function(key){
            var va = main.trim($scope.list[key].u.userpass);
            if(va.length < 1){
                this.userpassRes = "ok";
                this.userpassText = ""; 
            }
            else if(main.charLength(va) < 6){
                this.userpassRes = "error";
                this.userpassText = "登录密码不能小于6位";
            }
            else if(main.charLength(va) > 25){
                this.userpassRes = "error";
                this.userpassText = "登录密码不能大于25位";
            }
            else{
                this.userpassRes = "ok";
                this.userpassText = ""; 
            }
        },
        setUpdate: function(id){ this.updateid = id; },
        submit: function(key){
            if(this.usernameRes == "init") this.usernameTest(key);
            if(this.userpassRes == "init") this.userpassTest(key);
            if(this.usernameRes == "ok" && this.userpassRes == "ok" && $scope.list[key]){
                var data = main.copy($scope.list[key].u);
                data._action = "post";
                data._type = "user";
                web.post("var/admin.php", data, function(data){
                    if(data.res){
                        debug.success("修改新用户成功");
                        $scope.updateTest.updateid = 0;
                        $scope.updateTest.reset();
                        init($scope.pager.page);
                    }
                    else debug.error("修改新用户失败：" + data.msg);
                });
            }
            return false;
        },
        reset: function(key, en){
            if(key) $scope.list[key].u = main.copy($scope.list[key].n);
            this.usernameRes = "init";
            this.usernameText = "";
            this.userpassRes = "init";
            this.userpassText = "";
            if(en) en.preventDefault();
            return false;
        },
        cancel: function(key){
            this.updateid = 0;
            this.reset(key);
        }
    };
    var makeList = function(list){
        $scope.list = {};
        main.each(list, function(i, v){
            v.usercreatetime = extra.getDateTime(v.usercreatetime);
            $scope.list["u_" + v.userid] = {n: v, u: main.copy(v)};
        });
    };
    var init = function(page){
        web.post("var/admin.php", {_action: "getall", _type: "user", valid: -1, page: page, rows: 10}, function(data){
            $scope.pager.page = data.page.page;
            $scope.pager.total = data.page.totalpage;
            makeList(data.list);
        });
    };
    $scope.newTest.resetNew();
    web.post("var/admin.php", {_action: "get", _type: "userTypes"}, function(data){
        if(data.res) $scope.newTest.usertypes = data.obj; 
    });
    var index = $routeParams.index ? $routeParams.index : 1;
    init(index);
});