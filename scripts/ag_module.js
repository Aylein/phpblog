var app = angular.module("app", []);
var Param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
    for(name in obj) {
        value = obj[name];
        if(value instanceof Array) {
            for(i=0; i<value.length; ++i) {
                subValue = value[i];
                fullSubName = name + '[' + i + ']';
                innerObj = {};
                innerObj[fullSubName] = subValue;
                query += param(innerObj) + '&';
            }
        }
        else if(value instanceof Object) {
            for(subName in value) {
                subValue = value[subName];
                fullSubName = name + '[' + subName + ']';
                innerObj = {};
                innerObj[fullSubName] = subValue;
                query += param(innerObj) + '&';
            }
        }
        else if(value !== undefined && value !== null)
            query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }
    return query.length ? query.substr(0, query.length - 1) : query;
};
var web_Config = function(){
    this.type = "get";
    this.url = "";
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
app.service("web", function($http){
    this.http = function(op){
        op = web_Extend(op);
        $http({
            method: op.type,
            url: op.url,
            data: op.param,
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
    this.post = function(url, param, callback){
        var op = new web_Config();
        op.type = "post";
        op.url = url;
        op.param = param;
        op.headers = {"Content-Type": "application/x-www-form-urlencoded"};
        op.callback = callback;
        this.http(op);
    };
});