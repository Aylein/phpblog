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
app.directive("aoCs", function(dom, main){
    return {
        restrict: "AE",
        replace: true,
        scope: {model: "="},
        templateUrl: "/require/ngComment.html",
        link: function($scope, $elem, $attr){
            var aoCs = $scope.aoSc = {
                key: $scope.model.comid,
                show: $scope.model.show || false,
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
                else if(e.keyCode == 13 && e.ctrlKey) aoCs._send(); //control + enter
                else if(e.keyCode == 8 && e.ctrlKey) this.innerHTML = ""; //control + backspace
            };
            aoCs._send = function(){
                var _content = dom.get("sc_content_" + this.key), send = dom.get("sub_coin_" + this.key);
                _content.blur();
                _content.focus();
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
        link: function($scope, $elem, $attrs){
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