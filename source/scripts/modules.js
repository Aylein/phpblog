"use strict";
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
    }).when("/resume", {
        templateUrl: "/view/ngResume.html"
    }).when("/admin", {
        templateUrl: "/view/ngAdmin.php",
        controller: "adminController"
    }).when("/admintype", {
        templateUrl: "/view/ngAdminType.php",
        controller: "adminTypeController"
    }).when("/adminstage", {
        templateUrl: "/view/ngAdminStage.php",
        controller: "stageController"
    }).when("/adminuser", {
        templateUrl: "/view/ngAdminUser.php",
        controller: "adminUserController"
    }).when("/adminuser/:index", {
        templateUrl: "/view/ngAdminUser.php",
        controller: "adminUserController"
    }).otherwise({redirectTo: "/about"});
});
app.filter('to_trusted', function ($sce) {
    return function (text) {
        if(typeof text=='string') return $sce.trustAsHtml(text);
    }
});
app.service("main", function(){
    var re_typeof = /^\[object (\S+)\]$/;
    var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
    this.regex = {
        selector: {
            _select: /()(:)\[()\]/g,
            _tag: /^<([a-z]+)([^<>]*)>([^<>]*)/,
            _id: /^#(\S+)$/,
            _class: /^.(\S+)$/,
            _type: /\(([\S\s]+)\)/,
            _attr: /^([\S]*)=([\S]*)|([\S]*)="([\S]*)"$/,
            _attrs: /([\S]*="[\s\S]*")|(\S)*[^\s]|([\S]*=[\s]*)[^\s]/g
        },
        navi: {
            isWinNT: /Windows NT (\d+.\d+)[\.\d+]*/,
            isLinux: /Linux (\S+)/,
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
        },
        extra: {
            isNumber: /^\d+$/,
            isInt: /^[\-1-9][0-9]{0,11}$/,
            isFloat: /^[\-1-9][0-9]{0,11}(\.[0-9]{0,3}[1-9])?$/,
            isEmail: /^[\w\-_\.]+@[\w]+(\.[a-z\d]+)+$/,
            isMobile: /^[1][3|5|7|8][\d]{9}$/,
            isTelephone: /^(\d{3,4}[\-|\s])?\d{7,8}$/,
            isChinese: /^[\u4E00-\u9FA5]+$/,
            _byte: /[^\x00-\xff]/ig,
            blank: /\s+/
        },
        types: {
            _undefined: /^undefined|Undefined|null$/, //["undefined", "Undefined", "null"],
            _object: /^[o|O]bject$/, //["object", "Object"],
            _array: /^Array$/, //["Array"],
            _window: /^Window$/,
            _document: /^NodeList|HTMLCollection|HTMLAllCollection|HTMLDocument$/, //["HTMLCollection", "HTMLAllCollection", "HTMLDocument"],
            _element: /^HTML\S*Element$/, //["HTMLImageElement", "HTMLDivElement"],
            _domtoken: /^DOMTokenList$/,
            _function: /^[f|F]unction$/, //["function", "Function"],
            _number: /^[n|N]unction$/, //["number", "Number"],
            _string: /^[s|S]tring$/, //["string", "String"],
            _boolen: /^[b|B]oolean$/, //["boolean", "Boolean"]
        }
    };
    this.types = this.regex.types;
    this.trim = function(str){ return str == null ? "" : (str + "").replace(rtrim, ""); }; //from jquery
    this.obj = function(obj, deep){
        deep = deep || false;
        var i = 0;
        for(var name in obj){
            i++;
            if(!deep) return true;
        }
        return !deep ? false : i;
    };
    this.replace= function(src, target, rep){
        if(src == undefined || target == undefined || rep == undefined) return src;
        return src.toString().replace(new RegExp(target, "g"), rep.toString());
    };
    this.charcut = function(val, len) {
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
        return l;
    };
    this.typeof = function(obj, deep){    
        deep = deep || false;
        if(obj == undefined) return "undefined";
        return !deep && re_typeof.test(Object.prototype.toString.call(obj)) ? RegExp.$1 : typeof obj;
    };
    this.objc = function(obj, deep){
        deep = deep || false;
        var i = 0;
        for(var name in obj){
            var type = this.typeof(obj[name]);
            if(this.types._object.test(type) || this.types._array.test(type)){
                i++;
                if(!deep) return true;
            }
        }
        return !deep ? false : i;
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
                this.each(target, function(i, v, k){
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
    this.map = function(){
        var list = [];
        var len = arguments.length;
        if(len == 0) return list;
        var i = 0, f = arguments[i++];
        if(this.types._boolen.test(this.typeof(f))) i--;
        else f = false;
        var src = arguments[i++], callback = arguments[i++], args = arguments[i++];
        if(!this.types._function.test(this.typeof(callback))) return list;
        var type = this.typeof(src);
        if(this.isArrayLike(src)){
            for(var i = 0, z = src.length; i < z; i++){
                var _this = src[i], _args = this.merge(true, [], args);
                _args.push(i, _this);
                if(callback.apply(_this, _args)) list.push(f ? this.copy(_this) : _this);
            }
        }
        else if(this.types._object.test(type)){
            list = {};
            var i = 0;
            for(var key in src){
                var _this = src[key], _args = this.merge(true, [], args);
                _args.push(i, key, _this);
                if(callback.apply(_this, _args)) list[key] = f ? this.copy(_this) : _this;
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
        if(!target) return target;
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
                _args.push(i, _this, key);
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
        Cache[key] = value;
    };
    this.get = function(key){ return Cache[key] ? Cache[key] : undefined; };
    this.all = function(){ return main.copy(Cache); };
});
app.service("extra", function(main, cache){
    this.getDateTime = function(timestemp){
        var d = new Date(timestemp * 1000);
        return d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
    }
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
app.service("broswer", function(main){
    var ua = window.navigator.userAgent;
    var types = main.regex.navi;
    var navi = function(){
        this.en = undefined;
        this.env = undefined;
        this.bs = undefined;
        this.bsv = undefined;
        this.os = undefined;
    };
    navi.prototype = {
        toString: function(){
            return "Rendering Engine : " + (this.en || "") + " " + (this.env || "") + 
                ", Browser : " +  (this.bs || "") + " " + (this.bsv || "")  + 
                ", Operating System : " + (this.os || "");
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
            na.bs = "IE";
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
                case "10.0": na.os = "Windows 10"; break;
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
app.service("dom", function(main){
    var tags = ("a abbr acronym address applet area article aside audio b base basefont bdi bdo " +
        "big blockquote body br button canvas caption center cite code col colgroup command datalist " +
        "dd del details dfn dialog dir div dl dt em embed fieldset figcaption figure font fomain form frame " +
        "frameset h1 h2 h3 h4 h5 h6 head header hr html i iframe img input ins kbd keygen label legend li link main map mark " +
        "menu menuitem meta meter nav noframes noscript object ol optgroup option output p param pre progress q rp " +
        "rt ruby s samp script section select small source span strike strong style sub summary sup table tbody td " +
        "textarea tfoot th thead time title tr track tt u ul var video wbr").split(" ");
    var attrs = ("class id name style type rows cols width height require checked selected readonly contenteditable " + 
        "placehoder").split(" ");
    var eventType = ("click dblclick mousedown mouseup mouseover mouseout mousemove ouseenter mouseleave keypress keydown keyup" +
        "blur focus change reset submit touchstart touchmove touchend touchcancel").split(" ");
    this.noUrl = "javascript: void(0);";
    this.Element = function(tag, opt){
        tag = tag || undefined, opt = opt || undefined;
        var element;
        if(tags.indexOf(tag) > -1) element = document.createElement(tag);
        else if(main.regex.selector._tag.test(tag)){
            var no = {
                tag: RegExp.$1,
                attr: RegExp.$2,
                text: RegExp.$3
            };
            element = this.Element(no.tag, {text: no.text});
            var ar = no.attr.match(main.regex.selector._attrs);
            if(ar != undefined && ar.length > 0){
                for(var i = 0, z = ar.length; i < z; i++)
                {
                    if(ar[i].indexOf("=") > -1)
                        this.attr(element, ar[i].split("=")[0], ar[i].split("=")[1].replace(/"/g, ""));
                    else this.attr(element, ar[i], true);
                }
            }
        }
        if(element != undefined && main.types._object.test(main.typeof(opt))){
            for(var name in opt) {
                if(opt[name] != undefined) {
                    if(name == "text") this.text(element, opt.text);
                    else this.attr(element, name, opt[name]);
                }
            }
        }
        return element;
    };
    this.get = function(id){
        return document.getElementById(id);
    };
    this.clear = function(elem){
        while(elem.lastChild) elem.removeChild(elem.lastChild);
        return elem;
    };
    this.append = function(elem, _elem, deep){
        deep = deep || false;
        var type = main.typeof(elem);
        if(!main.types._element.test(type)) return elem;
        type = main.typeof(_elem);
        if(!main.types._element.test(type)) return elem;
        if(elem.appendChild == undefined) return elem;
        elem.appendChild(deep ? _elem.cloneNode(1) : _elem);
        return elem;
    };
    this.remove = function(elem, _elem){
        elem.removeChild(_elem);
    };
    this.on = function(event, elem, callback, bs){
        if(arguments.length < 3) return elem;
        var type = main.typeof(elem);
        if(!main.types._element.test(type) || eventType.indexOf(event) <= -1 ||
            !main.types._function.test(main.typeof(callback))) return elem;
        bs = bs || false;
        if(elem.addEventListener)
            elem.addEventListener(event, callback, bs);
        else if(elem.attachEvent)
            elem.attachEvent("on" + event, callback);
        else elem["on" + event] = callback;
        return elem;
    };
    this.no = function(e){
        if(e.preventDefault) e.preventDefault();
        else window.event.returnValue = false;
    };
    this.html = function(elem, html){
        var type = main.typeof(elem), _this = this;
        if(!main.types._element.test(type)) return false;
        if(html != undefined){
            this.clear(elem);
            type = main.typeof(html);
            if(main.types._element.test(type)) {
                if(elem.innerHTML != undefined) elem.innerHTML = html.toString();
                else return false;
            }
            else if(main.isArrayLike(html)){
                main.each(html, function(){
                    if(main.types._element.test(main.typeof(this)))
                        _this.append(elem, this);
                });
            }
            else if(main.types._string.test(type)){
                elem.innerHTML = html;
            }
            return elem;
        }
        else return elem.innerHTML != undefined ? elem.innerHTML.toString() : "";
    };
    this.text = function(elem, text){
        elem = elem || {};
        var bo = text == undefined;
        var fun = elem.innerText != undefined ? "innerText" : undefined;
        fun = fun == undefined && elem.textContent ? "textContent" : fun;
        return fun != undefined ? (bo ? elem[fun] : elem[fun] = text.toString(), elem) : undefined;
    };
    this.attr = function(elem, key, value){
        elem = elem || {};
        if(key == undefined || !elem.getAttribute) return elem;
        if(value == undefined) return elem.getAttribute(key.toString());
        else main.types._boolen.test(main.typeof(value)) ?
            (value ? elem.setAttribute(key.toString(), key.toString()) : this.removeAttr(elem, key)) :
            elem.setAttribute(key.toString(), value.toString());
        return elem;
    };
    this.removeAttr = function(elem, key){
        elem = elem || {};
        if(key == undefined || !elem.removeAttribute) throw elem;
        elem.removeAttribute(key.toString());
        return elem;
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
    this.tangoClass = function(elem, clsName){
        if(elem.classList){
            elem.classList.toggle(clsName);
            return;
        }
        if(elem.className.indexOf(clsName) < 0) elem.className += " " + clsName;
        else elem.className = elem.className.replace(clsName, "");
    };
    this.addClass = function(elem, clsName){
        if(elem.classList){
            clsName = clsName.split(" ");
            for(var i = 0, z = clsName.length; i < z; i++)
                if(clsName[i]) elem.classList.add(clsName[i]);
            return;
        }
        if(elem.className.indexOf(clsName) < 0) elem.className += " " + clsName;
    };
    this.removeClass = function(elem, clsName){
        if(elem.classList && elem.classList.contains(clsName)){
            clsName = clsName.split(" ");
            for(var i = 0, z = clsName.length; i < z; i++)
                elem.classList.remove(clsName[i]);
            return;
        }
        if(elem.className.indexOf(clsName) > 0) 
            elem.className = elem.className.replace(clsName, "");
    };
    this.classExsits = function(elem, clsName){
        return elem.className.indexOf(clsName) > -1;
    };
    this.style = function(elem, key, value){
        if(main.types._object.test(main.typeof(key)))
            for(var i in key)
                this.style(elem, i, key[i]);
        else if(value) elem.style[key] = value;
        else return elem.style[key];
    };
    this.styleText = function(elem, value){
        elem.style.cssText = value.toString();
    };
});
app.service("debug", function(dom, main){
    var DV = dom.Element("section");
    dom.addClass(DV, "ao_debug");
    dom.append(document.body, DV);
    this.add = function(type, str){
        var div = dom.Element("div");
        switch(type){
            case "success": dom.addClass(div, "ao_debug_success"); break;
            case "warnning": dom.addClass(div, "ao_debug_warnning"); break;
            case "error": dom.addClass(div, "ao_debug_error"); break;
            case "working": dom.addClass(div, "ao_debug_working"); break;
            default: dom.addClass(div, "ao_debug_default"); break;
        };
        dom.text(div, str);
        dom.append(DV, div);
        var br = dom.Element("br");
        dom.append(DV, br);
        setTimeout(function(){
            dom.remove(DV, div);
            dom.remove(DV, br);
        }, (function(){
            switch(type){
                case "warnning": 
                case "error": return 15000;
                case "success":
                case "working":
                default: return 5000;
            };
        })());
    };
    var _this = this;
    main.each(["success", "warnning", "error", "working"], function(){
        var type = this;
        _this[this] = function(msg){
          _this.add(type, msg);  
        };
    });
});
app.service("cv", function(main, dom){
    var Default = function(){
        this.sign = "ao",
		this.cover = {clsname: "", style: {}, click: false};
		this.dialog = {clsname: "", style: {}, callback: function(){}, show: function(){}};
		this.header = {clsname: "", style: {}}
		this.title = {text: "", html: "", clsname: "", style: {}};
		this.close = {text: "×", html: "", clsname: "", style: {}, click: false};
		this.content = {id: "", html: "", text: "", clsname: "", style: {}};
		this.footer = {clsname: "", style: {}};
		this.button = [];
    };
    var CV = function(opt){
		opt = opt || {};
		var def = new Default();
		for(var key in opt)
			if(def[key] && main.types._object.test(main.typeof(def[key])) && main.types._object.test(main.typeof(opt[key])))
				opt[key] = main.extend(def[key], opt[key]);
		opt = main.extend(def, opt);
        return new this.init(opt);
    };
    CV.prototype = {
        init: function(opt){
			var _this = this;
            this.opt = opt;
            this._main = dom.Element("section");
            dom.style(this._main, "display", "none");
            
            if(this.opt.cover){
                this._cover = dom.Element("div");
                dom.addClass(this._cover, this.opt.sign + "_cover " + this.opt.cover.clsname);
				if(this.opt.cover.style && main.obj(opt.cover.style))
					for(var key in this.opt.cover.style)
						dom.style(this._cover, key, this.opt.cover.style[key]);
                dom.style(this._cover, "z-index", "2");
				if(this.opt.cover.click && main.types._function.test(main.typeof(opt.cover.click)))
					dom.on("click", this._cover, function(){ opt.cover.click.call(_this); });
				dom.append(this._main, this._cover);
            }
            else this._cover = undefined;
            
            if(this.opt.dialog){
                this._dialog = dom.Element("div");
                dom.addClass(this._dialog, this.opt.sign + "_dialog " + this.opt.dialog.clsname);
				if(this.opt.dialog.style && main.obj(opt.dialog.style))
					for(var key in this.opt.dialog.style)
						dom.style(this._dialog, key, this.opt.dialog.style[key]);
                dom.style(this._dialog, "z-index", "3");
            }
            else return;
            
            this.form = dom.Element("form");
            this.form.method = "post";
            dom.append(this._dialog, this.form);
            
            if(this.opt.header){
                this._header = dom.Element("div");
                dom.addClass(this._header, this.opt.sign + "_header " + this.opt.header.clsname);
				if(this.opt.header.style && main.obj(opt.header.style))
					for(var key in this.opt.header.style)
						dom.style(this._header, key, this.opt.header.style[key]);
                        
                if(this.opt.title){
                    this._title = dom.Element("span");
                    dom.addClass(this._title, this.opt.sign + "_title " + this.opt.title.clsname);
                    if(this.opt.title.style && main.obj(opt.title.style))
                        for(var key in this.opt.title.style)
                            dom.style(this._title, key, this.opt.title.style[key]);
                    if(this.opt.title.text) dom.text(this._title, this.opt.title.text);
                    if(this.opt.title.html) dom.html(this._title, this.opt.title.html);
                    dom.append(this._header, this._title);
                }
                else this._title = undefined;
                
                if(this.opt.close){
                    this._close = dom.Element("a");
                    dom.attr(this._close, "href", dom.noUrl);
                    dom.addClass(this._close, this.opt.sign + "_close " + this.opt.close.clsname);
                    if(this.opt.close.style && main.obj(opt.close.style))
                        for(var key in this.opt.close.style)
                            dom.style(this._close, key, this.opt.close.style[key]);
                    if(this.opt.close.text) dom.text(this._close, this.opt.close.text);
                    if(this.opt.close.html) dom.html(this._close, this.opt.close.html);
                    dom.on("click", this._close, function(){
                        if(!_this.opt.close.click || !main.types._function.test(main.typeof(_this.opt.close.click)) || _this.opt.close.click(_this) !== false) _this.hide();
                    });
                    dom.append(this._header, this._close);
                }
                else this._close = undefined;
                dom.append(this.form, this._header);
            }
            else this._header = undefined;
            
            if(this.opt.content){
                this._content = dom.Element("div");
                dom.addClass(this._content, this.opt.sign + "_content " + this.opt.content.clsname);
				if(this.opt.content.style && main.obj(opt.content.style))
					for(var key in this.opt.content.style)
						dom.style(this._content, key, this.opt.content.style[key]);
                if(this.opt.content.text) dom.text(this._content, this.opt.content.text);
                if(this.opt.content.html) dom.html(this._content, this.opt.content.html);
                if(this.opt.content.id){
                    this._content_c = dom.get(this.opt.content.id);
                    dom.show(this._content_c);
                    dom.append(this._content, this._content_c);
                }
                dom.append(this.form, this._content);
            }
            else this._content = undefined;
            
            this._button = {};
            if(this.opt.footer){
                this._footer = dom.Element("div");
                dom.addClass(this._footer, this.opt.sign + "_footer " + this.opt.footer.clsname);
				if(this.opt.footer.style && main.obj(opt.footer.style))
					for(var key in this.opt.footer.style)
						dom.style(this._footer, key, this.opt.footer.style[key]);
                dom.append(this.dialog, this._footer);
                
                if(this.opt.button.length > 0){
                    main.each(this.opt.button, function(index){
                        var that = this, key = this.key ? this.key : "key_" + index, o = dom.Element("a");
                        dom.attr(o, "href", dom.noUrl);
                        dom.addClass(o, _this.opt.sign + "_button" + (this.clsname ? " " + this.clsname : ""));
                        if(this.style && main.obj(this.style))
                            for(var key in this.style)
                                dom.style(o, key, this.style[key]);
                        if(this.text) dom.text(o, this.text);
                        if(this.html) dom.html(o, this.html);
                        if(this.click && main.types._function.test(main.typeof(this.click))){
                            if(this.type == "submit") dom.on("submit", _this.form, function(e){ that.click(_this); e = e || window.event; e.preventDefault(); return false; });
                            else if(this.type == "reset") dom.on("reset", _this.form, function(e){ that.click(_this); });
                            dom.on("click", o, function(){ that.click(_this); });
                        }
                        _this._button[key] = o;
                        dom.append(_this._footer, o);
                    });
                }
                dom.append(this.form, this._footer);
            }
            else this._footer = undefined;
            
            dom.append(this._main, this._dialog);
            dom.append(document.body, this._main);
        },
        focus: function(id){
            if(id) setTimeout(function(){ dom.get(id).focus(); }, 1);
            return this;
        },
        show: function(){
            dom.style(document.body, "overflow", "hidden");
            dom.show(this._cover);
            dom.show(this._main);
            return this;
        },
        hide: function(){
            dom.style(document.body, "overflow", "auto");
            dom.hide(this._main);
            dom.hide(this._cover);
            dom.remove(document.body, this._main);
            return this;
        }
    };
    CV.prototype.init.prototype = CV.prototype;
    this.init = function(opt){
        return new CV(opt).show();
    };
    this.prop = function(title, message, type, callback){
        return new CV({
            title: {text: title},
            content: {html: (message ? message + "&nbsp;&nbsp;&nbsp;&nbsp;" : "") + "<input type=\"" + type + "\" id=\"cv_input\">"},
            button: [
            {
                key: "yes",
                text: "确定",
                type: "submit",
                click: function(v){
                    var cv_input = dom.get("cv_input");
                    if(!callback || main.types._function.test(main.typeof(callback)) && callback(true, main.trim(cv_input.value), v) !== false) v.hide(); 
                }
            },
            {
                key: "no",
                text: "取消",
                click: function(v){
                    var cv_input = dom.get("cv_input");
                    if(!callback || main.types._function.test(main.typeof(callback)) && callback(false, main.trim(cv_input.value), v) !== false) v.hide(); 
                }
            }]
        }).show().focus("cv_input");
    };
    this.alert = function(title, message, callback){
        return new CV({
            title: {text: title},
            content: {html: message},
            button: [{
                key: "yes",
                text: "确定",
                click: function(v){
                    if(!callback || main.types._function.test(main.typeof(callback)) && callback(v) !== false) v.hide(); 
                }
            }]
        }).show();
    };
    this.confirm = function(title, message, callback){
        return new CV({
            title: {text: title},
            content: {html: message},
            button: [
            {
                key: "yes",
                text: "确定",
                click: function(v){
                    if(!callback || main.types._function.test(main.typeof(callback)) && callback(true, v) !== false) v.hide(); 
                }
            },
            {
                key: "no",
                text: "取消",
                click: function(v){
                    if(!callback || main.types._function.test(main.typeof(callback)) && callback(false, v) !== false) v.hide(); 
                }
            }]
        }).show();
    };
});
app.service("cookie", function(){
    var reg_trim = /^(\s+)|(\s+)$/g;
    this.trim = function(str){
        return str.replace(reg_trim, function($1){ return ""; });
    };
    this.cookieText = function(){
        return document.cookie;
    };
    this.cookieObj = function(){
        var _cookie = this.cookieText().split(";"), obj = {};
        for(var i = 0, z = _cookie.length; i < z; i++){
            var _kv = _cookie[i].split("=");
            obj[_kv[0]] = _kv[1];
        }
        return obj;
    };
    this.get = function(key){
        return this.cookieObj()[key];
    };
    this.set = function(key, value, domain, path, expires){
        var str = key + "=" + value + ";";
        if(domain) str += "domain=" + domain + ";";
        if(path) str += "patch=" + path + ";";
        if(expires) str += "expires=" + expires + ";";
        document.cookie = str;
    };
    this.delete = function(key){
        var date = new Date();
        date.setTime(date.getTime() - 600000);
        var str = key + "=v; expires=" + date.getTime();
        document.cookie = str;
    };
    this.clear = function(){
        var date = new Date(), _cookie = this.cookieText().split(";");
        date.setTime(date.getTime() - 600000);
        for(var i = 0, z = _cookie.length; i < z; i++)
            document.cookie = _cookie[i] + "; expires=" + date.getTime();
    };
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