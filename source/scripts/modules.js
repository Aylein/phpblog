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
    var re_typeof = /^\[object (\S+)\]$/;
    var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
    this.trim = function(str){ return str == null ? "" : (str + "").replace(rtrim, ""); }; //from jquery
    this.obj = function(obj, deep){
        deep = deep != undefined ? deep : true;
        var i = 0;
        for(var name in obj){
            i++;
            if(deep) return true;
        }
        return deep ? false : i;
    };
    this.objc = function(obj, deep){
        deep = deep != undefined ? false : true;
        var i = 0;
        for(var name in obj){
            var type = this.typeof(obj[name]);
            if(this.types._object.test(type)){
                i++;
                if(deep) return true;
            }
        }
        return deep ? false : i;
    };
    this.replace= function(src, target, rep){
        if(src == undefined || target == undefined || rep == undefined) return src;
        return src.toString().replace(new RegExp(target, "g"), rep.toString());
    };
    this.cut = function(val, len) {
        var l = 0, z = "";
        for (var i = 0; i < val.length; i++) {
            z += val[i];
            var length = val.charCodeAt(i);
            if (length >= 0 && length <= 128) l += 0.5;
            else l += 1;
            if (l >= len) {
                if (l < val.length) z += "...";
                break;
            }
        }
        return z;
    },
    this.charLength = function(val){
        var l = 0;
        for (var i = 0; i < val.length; i++) {
            var length = val.charCodeAt(i);
            if (length >= 0 && length <= 128) l += 1;
            else l += 2;
        }
        return z;
    };
    this.types = {
        _undefined: /^undefined|Undefined|null$/, //["undefined", "Undefined", "null"],
        _object: /^[o|O]bject$/, //["object", "Object"],
        _array: /^Array$/, //["Array"],
        _window: /^Window$/,
        _document: /^NodeList|HTMLCollection|HTMLAllCollection|HTMLDocument$/, //["HTMLCollection", "HTMLAllCollection", "HTMLDocument"],
        _element: /^HTML\S+Element$/, //["HTMLImageElement", "HTMLDivElement"],
        _domtoken: /^DOMTokenList$/,
        _function: /^[f|F]unction$/, //["function", "Function"],
        _number: /^[n|N]unction$/, //["number", "Number"],
        _string: /^[s|S]tring$/, //["string", "String"],
        _boolen: /^[b|B]oolean$/, //["boolean", "Boolean"]
    };
    this.typeof = function(obj, deep){    
        deep = deep || true;
        if(obj == undefined) return "undefined";
        return deep && re_typeof.test(Object.prototype.toString.call(obj)) ? RegExp.$1 : typeof obj;
    };
    this.isArrayLike = function(array){
        var type = this.typeof(array, 1);
        if(type == "undefined") return false;
        if(this.types._array.test(type) || this.types._document.test(type)) return true;
        return this.types._object.test(type) && array.splice != undefined && array.length >= 0;
    };
    this.extend = function(){
        var len = arguments.length;
        if(len == 0) return {};
        var i = 0, n = 0, f = arguments[i++], src, target, main = this;
        if(this.types._boolen.test(this.typeof(f))) src = arguments[i++];
        else {src = f; f = false;}
        var type = this.typeof(src), _type;
        if(!this.types._object.test(type)) src = this.extend({}, src);
        if(f && this.obj(src)) src = this.extend(true, {}, src);
        if(len <= i) return src ? src : {};
        n = this.obj(src, false);
        while(i < len){
            target = arguments[i++];
            if(!target) continue;
            _type = this.typeof(target);
            if(this.types._object.test(_type)){
                this.each(target, function(i, k, v){
                    src[k] = f ? main.copy(v) : v;
                });
            }
            else if(this.isArrayLike(target)){
                this.each(target, function(i, v){
                    src[n] = f ? main.copy(v) : v;
                    n++;
                });
            }
            else {
                src[n.toString()] = target;
                n++;
            }
        }
        return src;
    };
    this.merge = function(){
        var len = arguments.length;
        if(len == 0) return [];
        var i = 0, f = arguments[i++], src, target, main = this;
        if(this.types._boolen.test(this.typeof(f))) src = arguments[i++];
        else {src = f; f = false;}
        var type = this.typeof(src), _type;
        if(!this.types._array.test(type)) src = this.merge([], src);
        if(f && src.length > 1) src = this.merge(true, [], src);
        if(len <= i) return src ? src : [];
        while(i < len){
            target = arguments[i++];
            if(!target) continue;
            _type = this.typeof(target);
            if(this.types._object.test(_type) || this.isArrayLike(target))
                this.each(target, function(i, v){ src.push(f ? main.copy(v) : v); });
            else src.push(target);
        }
        return src;
    };
    this.map = function(src, callback, args){
        var list = [];
        if(!this.types._function.test(this.typeof(callback))) return list;
        var type = this.typeof(src);
        if(this.isArrayLike(src)){
            for(var i = 0, z = src.length; i < z; i++){
                var _this = src[i], _args = this.merge(true, [], args);
                _args.push(i, _this);
                if(callback.apply(_this, _args)) list.push(this.copy(_this));
            }
        }
        else if(this.types._object.test(type)){
            list = {};
            var i = 0;
            for(var key in src){
                var _this = src[key], _args = this.merge(true, [], args);
                _args.push(i, key, _this);
                if(callback.apply(_this, _args)) list[key] = this.copy(_this);
                i++;
            }
        }
        else {
            var _args = this.merge(true, [], args);
            if(callback.apply(src, _args)) list = src;
        }
        return list;
    };
    this.copy = function(target){
        if(!target) return undefined;
        var type = this.typeof(target);
        if(this.isArrayLike(target)) return this.merge(true, target);
        else if(this.types._object.test(type)) return this.extend(true, target);
        else return target;
    };
    this.each = function(src, callback, args){
        if(!this.types._function.test(this.typeof(callback))) return src;
        var type = this.typeof(src);
        if(this.isArrayLike(src)){
            for(var i = 0, z = src.length; i < z; i++){
                var _this = src[i], _args = this.merge(true, [], args);
                _args.push(i, _this);
                if(callback.apply(_this, _args)) break;
            }
        }
        else if(this.types._object.test(type)){
            var i = 0;
            for(var key in src){
                var _this = src[key], _args = this.merge(true, [], args);
                _args.push(i, key, _this);
                if(callback.apply(_this, _args)) break;
                i++;
            }
        }
        else {
            var _args = this.merge(true, [], args);
            callback.apply(src, _args);
        }
    };
});
app.service("cache", function(main){
    var Cache = {};
    this.exists = function(key){ return Cache[key] ? true : false; };
    this.set = function(key, value, deep){ 
        deep = deep || false;
        if(!deep && this.exists(key)) return;
        ache[key] = value;
    };
    this.get = function(key){ return Cache[key] ? Cache[key] : undefined; };
    this.all = function(){ return main.copy(Cache); };
});
app.service("extra", function(main, cache){
    this.random = function(len){
        len = len && parseInt(len) > 5 ? len : 5;
        cache.__random = cache.__random || [];
        var ca = cache.__random, l = ca.length, bo = false;
        var str = "", arr = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n",
            "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z","A", "B", "C", "D",
            "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
            "U", "V", "W", "X", "Y", "Z", "_", "1", "2", "3", "4", "5", "6", "7", "8", "9", 
            "0"];
        for(var i = 0; i < len; i++) str += arr[Math.floor(Math.random() * arr.length)];
        for(var i = 0; i < l; i++) if(ca[i] == str){ bo = true; break; }
        if(bo) return this.random(len);
        return str;
    };
});
app.service("broswer", function(){
    var ua = window.navigator.userAgent;
    var types = {
        isWinNT: /Windows NT (\d+.\d+)[\.\d+]*/,
        isMac: /Mac OS X (\S+)/,
        isLikeMac: /Mac OS X/,
        isIPhone: /iPhone OS (\S+)/,
        isIpad: /iPad; CPU OS (\S+)/,
        isGecko: /Gecko\/(\d+.\d+)[\.\d+]*/,
        isAWK: /AppleWebKit\/(\d+.\d+)[\.\d+]*/,
        isTrident: /Trident\/(\d+.\d+)[\.\d+]*/,
        isFireFox: /Firefox\/(\d+.\d+)[\.\d+]*/,
        isIE: /MSIE (\d+.\d+)[\.\d+]*/,
        isChrome: /Chrome\/(\d+.\d+)[\.\d+]*/,
        isSafari: /Safari\/(\d+.\d+)[\.\d+]*/,
        isOpera: /OPR\/(\d+.\d+)[\.\d+]*/
    };
    var navi = function(){
        this.en = undefined;
        this.env = undefined;
        this.bs = undefined;
        this.bsv = undefined;
        this.os = undefined;
    };
    navi.prototype = {
        toString: function(){
            return "Rendering Engine : " + (this.en||"") + " " + (this.env||"") + ", Browser : " +  (this.bs||"") + " " + (this.bsv||"")  + ", Operating System : " + (this.os||"");
        }
    };
    var na = new navi();
    var init = function(){
        if(window.opera){
            na.bs = "Opera";
            na.bsv = window.opera.version();
        }
        else if(types.isAWK.test(ua)){
            na.en = "AppleWebKit";
            na.env = RegExp.$1;
            if (types.isOpera.exec(ua)){
                na.bs = "Opera";
                na.bsv = RegExp.$1;
            }
            else if(types.isChrome.exec(ua)){
                na.bs = "Chrome";
                na.bsv = RegExp.$1;
            }
            else if(types.isSafari.exec(ua)){
                na.bs = "Safari";
                na.bsv = RegExp.$1;
            }
        } 
        else if(types.isGecko.test(ua)){
            na.en = "Gecko";
            na.env = RegExp.$1;
            if(types.isFireFox.exec(ua)){
                na.bs = "FireFox";
                na.bsv = RegExp.$1;
            }
        } 
        else if(types.isIE.test(ua)){
            na.en = "MSIE";
            na.env = RegExp.$1;
            na.bs = "IE";
            na.bsv = RegExp.$1;
        } 
        else if(types.isTrident.test(ua)){
            na.en = "Trident";
            na.env = RegExp.$1;
            na.bs = "IE"
            switch(na.env){
                case "4.0": na.bsv = "8.0"; break;
                case "5.0": na.bsv = "9.0"; break;
                case "6.0": na.bsv = "10.0"; break;
                case "7.0": na.bsv = "11.0"; break;
                default: break;
            }
        }
        if(types.isWinNT.test(ua)){
            switch(RegExp.$1){
                case "5.0": na.os = "Windows 2000"; break;
                case "5.1": na.os = "Windows XP"; break;
                case "6.0": na.os = "Windows Vista"; break;
                case "6.1": na.os = "Windows 7"; break;
                case "6.2": na.os = "Windows 8"; break;
                case "6.3": na.os = "Windows 8.1"; break;
                default: na.os = "Windows NT " + RegExp["$2"]; break;
            }
        } 
        else if(types.isLikeMac.test(ua)){
            if(types.isIPhone.test(ua)) na.os = "IPhone OS " + RegExp.$1;
            if (types.isIpad.test(ua)) na.os = "iPad CPU OS " + RegExp.$1;
        } 
        else if(types.isMac.test(ua))
            if(types.isIPhone.test(ua)) na.os = "Mac OS X " + RegExp.$1;
    };
    init();
    this.en = na.en;
    this.env = na.env;
    this.bs = na.bs;
    this.bsv = na.bsv;
    this.os = na.os;
    this.toString = na.toString;
});
app.service("dom", function(){
    this.get = function(id){
        return document.getElementById(id);
    };
    this.on = function(type, elem, callback){
        if(elem.addEventListener)
            elem.addEventListener(type, callback, false);
        else if(elem.attachEvent)
            elem.attachEvent("on" + type, callback);
        else elem["on" + type] = callback;
    };
    this.no = function(e){
        if(e.preventDefault) e.preventDefault();
        else window.event.returnValue = false;
    };
    this.hide = function(elem){
        elem.style.display = "none";
    };
    this.show = function(elem){
        elem.style.display = "block";
    };
    this.tango = function(elem){
        if(elem.style.display && elem.style.display == "none")
            elem.style.display = "block";
        else elem.style.display = "none";
    };
    this.html = function(elem, html){
        var bo = html == undefined;
        return bo ? elem.innerHTML : elem.innerHTML = html.toString();
    };
    this.text = function(elem, text){
        var bo = text == undefined;
        var fun = elem.innerText != undefined ? "innerText" : undefined;
        fun = fun == undefined && elem.textContent ? "textContent" : undefined;
        return fun != undefined ? (bo ? elem[fun] : elem[fun] = text.toString()) : "";
    };
});
app.service("cookie", function(){

});
app.service("web", function($http, main){
    var config = {
        type: "get",
        url: "",
        data: {},
        param: {},
        headers: {},
        callback: function(data){}
    };
    var param = function(obj){
        var query = "", name, value, fullSubName, subName, subValue, innerObj, i;
        for(name in obj){
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
    var _http = function(op){
        $http({
            method: op.type,
            url: op.url,
            param: op.param,
            data: op.data,
            headers: op.headers,
            transformRequest: param
        }).success(op.callback);
    };
    this.http = function(op){
        op = main.extend(true, config, op);
        _http(op);
    };
    this.get = function(url, param, callback){
        var op = main.copy(config);
        op.url = url;
        op.param = param;
        op.callback = callback;
        _http(op);
    };
    this.post = function(url, data, callback){
        var op = main.copy(config);
        op.type = "post";
        op.url = url;
        op.data = data;
        op.headers = {"Content-Type": "application/x-www-form-urlencoded"};
        op.callback = callback;
        _http(op);
    };
});