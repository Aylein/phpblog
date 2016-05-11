"use strict";
/* global angular */
var app = angular.module("app");
/* 可编辑div的双向绑定 https://www.zhihu.com/question/27156225 @机智的布鲁斯 */
app.directive("contenteditable", function(){
    return {
        restrict: "A",
        require: "?ngModel",
        link: function($scope, $element, $attrs, ngModel){
            if(!ngModel) return;
            ngModel.$render = function(){
                $element.html(ngModel.$viewValue || "");
            };
            $element.on("blur keyup change", function(){
                $scope.$apply(readViewText);
            });
            function readViewText() {
                var html = $element.html();
                if ($attrs.stripBr && html === "<br>"){
                    html = "";
                }
                ngModel.$setViewValue(html);
            }
        }
    }
});
/* angular 指令递归死循环的问题 http://blog.csdn.net/liuhe688/article/details/50966215 @liuhe688 */
app.directive("aoRp", function(extra){
    return {
        restrict: "AE",
        replace: true,
        scope: {model: "=", repeat: "="},
        templateUrl: "/require/ngRepeat.html",
        link: function($scope){
            $scope.key = extra.random(7);
            $scope.show = false;
            $scope.clab = 5;
            if($scope.repeat > 0) $scope.model.aorp_repeat = true;
            var init = function(){
                if($scope.repeat == 0 && $scope.model.aorp_repeat == true) $scope.show = false;
                else $scope.show = true;
            }
            $scope.makeShow = function(){ $scope.show = !$scope.show; };
            $scope.$watch("model.aorp_repeat", function(obj){ init(); }, true);
        }
    };
});
app.directive("aoPg", function(dom, main){
    return {
        restrict: "AE",
        replace: true,
        scope: {model: "="},
        templateUrl: "/require/ngPager.html",
        link: function($scope){
            var pgc = $scope.pager = {
                first: 1,
                pages: [],
                begin: 1
            };
            var init = function(){
                pgc.url = $scope.model.url || "javascript: void(0);";
                pgc.curLink = $scope.model.curLink || false;
                pgc.page = $scope.model.page;
                pgc.total = $scope.model.total;
                pgc.show = $scope.model.show;
                pgc.fn = function($event, index){
                    if(!pgc.curLink && (index > pgc.total || index < pgc.first || index == pgc.page)) return;
                    if($scope.model.callback && main.types._function.test(main.typeof($scope.model.callback))) $scope.model.callback.call($scope.model, index, $event);
                };
                
                pgc.show = pgc.total < pgc.show ? pgc.total : pgc.show;
                    
                if(pgc.page < 1) pgc._page = 0;
                else if(pgc.page > pgc.total) pgc._page = pgc.total + 1;
                else pgc._page = pgc.page;
                
                pgc.pref = pgc._page <= 1 ? 1 : pgc._page - 1;
                pgc.next = pgc._page >= pgc.total ? pgc.total : pgc._page + 1;
                pgc.middle = parseInt(pgc.show / 2) + 1;
                pgc._middle = parseInt(pgc.show / 2);
                
                if(pgc.total <= pgc.show || pgc._page <= pgc.middle) pgc.begin = 1;
                else if(pgc._page >= (pgc.show % 2 == 0 ? pgc.total - pgc._middle + 1 : pgc.total - pgc._middle)) pgc.begin = pgc.total - pgc.show + 1;
                else pgc.begin = pgc._page - pgc._middle;
                
                pgc.pages = [];
                for(var i = 1; i <= pgc.show; i++) pgc.pages.push(pgc.begin + i - 1);
            };
            $scope.$watch("model", function(obj){ init(); }, true);
        }
    };
});
app.directive("aoCs", function(dom, main, extra){
    return {
        restrict: "AE",
        replace: true,
        scope: {model: "="},
        templateUrl: "/require/ngComment.html",
        link: function($scope){
            var aoCs = $scope.aoSc = {
                key: $scope.model.key || extra.random(7),
                show: $scope.model.show || false,
                comment: "", //$scope.model.comment,
                callback: $scope.model.fn.callback || function(){},
                show_fn: $scope.model.fn.show || function(){ return true; },
                hide_fn: $scope.model.fn.hide || function(){ return true; },
                showHide: $scope.model.showHide === false ? false : true
            };
            aoCs._hide = function(){
                this.show = !this.show;
            };
            aoCs._tango = function(){
                dom.get("sc_content_" + this.key).focus();
                var elem = dom.get("sc_ac_img_" + this.key);
                if(elem.style.display == "block")
                    elem.style.display = "none";
                else elem.style.display = "block";
            };
            aoCs._img = function(src){
                document.execCommand('InsertImage', false, src);
                dom.get("sc_ac_img_" + this.key).style.display = "none";
            };
            aoCs._bold = function(){
                dom.get("sc_content_" + this.key).focus();
                document.execCommand("Bold");
            };
            aoCs._italic = function(){
                dom.get("sc_content_" + this.key).focus();
                document.execCommand("Italic");
            };
            aoCs._keyup = function(e){
                var _content = dom.get("sc_content_" + this.key);
                if(_content.contentEditable != "true") return;
                if(e.repeat) return;
                else if(e.keyCode == 27) document.execCommand("undo"); //撤销
                else if(e.keyCode == 9){
                    document.execCommand("Indent"); //tab
                    dom.no(e);
                }
                else if(e.keyCode == 13 && e.ctrlKey) setTimeout(function(){ aoCs._send(); }, 1); //control + enter
                else if(e.keyCode == 8 && e.ctrlKey) this.innerHTML = ""; //control + backspace
            };
            aoCs._send = function(){
                var _content = dom.get("sc_content_" + this.key), send = dom.get("sub_coin_" + this.key);
                _content.blur();
                _content.focus();
                if(main.types._function.test(main.typeof(this.callback))) this.callback.call($scope.model, this.comment, this.key, this);
            };
            aoCs._clear = function(){
                var _content = dom.get("sc_content_" + this.key);
                _content.innerHTML = "";
                _content.blur();
                _content.focus();
            };
            var init = function(){
                var src = "images/ac/ac_", p = ".png";
                aoCs.sc_ac = [];
                for(var i = 1; i <= 50; i++) aoCs.sc_ac.push(src + i + p);
            };
            init();
        }
    };
});
app.directive("aoNodes", function(main, cache){
    return {
        restrict: "E",
        replace: true,
        templateUrl: "/require/ngAdminHeader.html",
        link: function($scope){
            $scope.admin = cache.get("admin_nodes") || {
                nodes: {
                    contents: {
                        name: "Contents",
                        url: "#/admincont",
                        width: "w120",
                        cur: 0,
                        list: {
                            types: {cur: 0, name: "Types", url: "#/admintype"},
                            stages: {cur: 0, name: "Stages", url: "#/adminstage"},
                            documents: {cur: 0, name: "Documents", url: "#/admindoc"},
                            comments: {cur: 0, name: "Comments", url: "#/admincomm"}
                        }
                    },
                    admins: {
                        name: "Admins",
                        url: "#/admin",
                        width: "w80",
                        cur: 0,
                        list: {
                            mains: {cur: 0, name: "Mains", url: "#/adminmain"},
                            signs: {cur: 0, name: "Signs", url: "#/adminsign"},
                            signOns: {cur: 0, name: "SignOns", url: "#/adminsignon"}
                        }
                    },
                    members: {
                        name: "Members",
                        url: "#/adminmem",
                        width: "w80",
                        cur: 0,
                        list: {
                            users: {cur: 0, name: "Users", url: "#/adminuser"},
                            actions: {cur: 0, name: "Actions", url: "#/adminaction"}
                        }
                    }
                },
                cur: function(name, n, deep){
                    n = n || 1;
                    deep = deep || false;
                    var _i = n == 1 ? 0 : 1;
                    for(var i in this.nodes){
                        var o = this.nodes[i];
                        if(i != name){
                            if(deep) o.list[j].cur = _i;
                        }
                        else o.cur = n;
                        for(var j in o.list){
                            if(j != name){
                                if(deep) o.list[j].cur = _i;
                                continue;
                            }
                            o.list[j].cur = n;
                        }
                    }   
                },
                clear: function(){
                    for(var i in this.nodes){
                        var o = this.nodes[i];
                        o.cur = 0;
                        for(var j in o.list)
                            o.list[j].cur = 0;
                    }   
                }
            };
            if(!cache.exists("admin_nodes")) cache.set("admin_nodes", $scope.admin);
            if($scope.path){
                $scope.admin.clear();
                $scope.admin.cur($scope.path);
            }
        }
    };
});