var app = angular.module("app", ["ngRoute"]);
app.config(function($routeProvider){
    $routeProvider.when("/index", {
        templateUrl: "/view/ngMain.html",
        controller: "urlController"
    }).when("/says", {
        templateUrl: "/view/ngSays.html",
        controller: "saysController"
    }).when("/about", {
        templateUrl: "/view/ngAbout.html",
        controller: "urlController"
    }).when("/found/:typeid", {
        templateUrl: "/view/ngFound.html",
        controller: "urlController"
    }).otherwise({redirectTo: "/about"});
});
app.service("main", function(){
    this.get = function(id){
        return document.getElementById(id);
    };
    this.on = function(type, elem, callback){
        if(elem.addEventListener)
            elem.addEventListener(type, callback, false);
        else if(elem.attachEvent)
            elem.attachEvent(type, callback);
    };
});
app.service("cookie", function(){

});
app.service("web", function($http){
    var web_Config = function(){
        this.type = "get";
        this.url = "";
        this.data = {};
        this.param = {};
        this.headers = {};
        this.callback = function(data){};
    };
    var web_Extend = function(op){
        var def = new web_Config();
        for(var i in op)
            if(def[i] != undefined)
                def[i] = op[i];
        return def;
    };
    var Param = function(obj) {
        var query = "", name, value, fullSubName, subName, subValue, innerObj, i;
        for(name in obj) {
            value = obj[name];
            if(value instanceof Array) {
                for(i=0; i<value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + "[" + i + "]";
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + "&";
                }
            }
            else if(value instanceof Object) {
                for(subName in value) {
                    subValue = value[subName];
                    fullSubName = name + "[" + subName + "]";
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + "&";
                }
            }
            else if(value !== undefined && value !== null)
                query += encodeURIComponent(name) + "=" + encodeURIComponent(value) + "&";
        }
        return query.length ? query.substr(0, query.length - 1) : query;
    };
    this.http = function(op){
        op = web_Extend(op);
        $http({
            method: op.type,
            url: op.url,
            param: op.param,
            data: op.data,
            headers: op.headers,
            transformRequest: Param
        }).success(op.callback);
    };
    this.get = function(url, param, callback){
        var op = new web_Config();
        op.url = url;
        op.param = param;
        op.callback = callback;
        this.http(op);
    };
    this.post = function(url, data, callback){
        var op = new web_Config();
        op.type = "post";
        op.url = url;
        op.data = data;
        op.headers = {"Content-Type": "application/x-www-form-urlencoded"};
        op.callback = callback;
        this.http(op);
    };
});