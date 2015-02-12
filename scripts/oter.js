(function() {
    //private
    //保存 window.$ 的值
    var _$ = window.$ || undefined;
    //保存 window.oter 的值
    var _Oter = window.Oter || undefined;
    var _oter = window.oter || undefined;

    //Array
    var arr = [];

    //oter
    oter = window.oter = window.Oter = window.$ = function(selector) {
        return new oter.fn.init(selector);
    };

    //browser
    var navi = function() {
        this.en = undefined;
        this.env = undefined;
        this.bs = undefined;
        this.bsv = undefined;
        this.os = undefined;
    };
    var browser = function() {
        var ua = window.navigator.userAgent;
        var na = new navi();
        if (window.opera) {
            na.bs = "Opera";
            na.bsv = window.opera.version();
        } else if (oter.regex.navi.isAWK.test(ua)) {
            na.en = "AppleWebKit";
            na.env = RegExp.$1;
            if (oter.regex.navi.isOpera.exec(ua)) {
                na.bs = "Opera";
                na.bsv = RegExp.$1;
            } else if (oter.regex.navi.isChrome.exec(ua)) {
                na.bs = "Chrome";
                na.bsv = RegExp.$1;
            } else if (oter.regex.navi.isSafari.exec(ua)) {
                na.bs = "Safari";
                na.bsv = RegExp.$1;
            }
        } else if (oter.regex.navi.isGecko.test(ua)) {
            na.en = "Gecko";
            na.env = RegExp.$1;
            if (oter.regex.navi.isFireFox.exec(ua)) {
                na.bs = "FireFox";
                na.bsv = RegExp.$1;
            }
        } else if (oter.regex.navi.isIE.test(ua)) {
            na.en = "MSIE";
            na.env = RegExp.$1;
            na.bs = "IE";
            na.bsv = RegExp.$1;
        } else if (oter.regex.navi.isTrident.test(ua)) {
            na.en = "Trident";
            na.env = RegExp.$1;
            na.bs = "IE"
            switch (na.env) {
                case "4.0":
                    na.bsv = "8.0";
                    break;
                case "5.0":
                    na.bsv = "9.0";
                    break;
                case "6.0":
                    na.bsv = "10.0";
                    break;
                case "7.0":
                    na.bsv = "11.0";
                    break;
                default:
                    break;
            }
        }
        if (oter.regex.navi.isWinNT.test(ua)) {
            switch (RegExp.$1) {
                case "5.0":
                    na.os = "Windows 2000";
                    break;
                case "5.1":
                    na.os = "Windows XP";
                    break;
                case "6.0":
                    na.os = "Windows Vista";
                    break;
                case "6.1":
                    na.os = "Windows 7";
                    break;
                case "6.2":
                    na.os = "Windows 8";
                    break;
                case "6.3":
                    na.os = "Windows 8.1";
                    break;
                default:
                    na.os = "Windows NT " + RegExp["$2"];
                    break;
            }
        } else if (oter.regex.navi.isLikeMac.test(ua)) {
            if (oter.regex.navi.isIPhone.test(ua)) {
                na.os = "IPhone OS " + RegExp.$1;
            }
            if (oter.regex.navi.isIpad.test(ua)) {
                na.os = "iPad CPU OS " + RegExp.$1;
            }
        } else if (oter.regex.navi.isMac.test(ua)) {
            if (oter.regex.navi.isIPhone.test(ua)) {
                na.os = "Mac OS X " + RegExp.$1;
            }
        }
        return na;
    };
    navi.prototype = {
        toString: function() {
            return "Rendering Engine : " + (this.en||"") + " " + (this.env||"") + ", Browser : " +  (this.bs||"") + " " + (this.bsv||"")  + ", Operating System : " + (this.os||"");
        }
    };

    //static
    //array
    oter.arr = [];
    //regex
    oter.regex = {
        selector: {
            _tag: /^(\S+)$/,
            _id: /^#(\S+)$/,
            _class: /^.(\S+)$/,
            _type: /\(([\S\s]+)\)/,
            _attr: /^\[([\S\s]+)\]$/,
            _attrs: /([a-z\.\-_"'\d]+=[a-z\.\:-_#"'\d\(\)]+)/g
        },
        navi: {
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
        }
    }
    //navigator
    oter.navi = new browser();
    //ajax
    oter.ajax = function(option){
        var _ajax = oter.ajax.prototype;
        option = this.extend(_ajax.defaults, option);
        var xhr = _ajax.XHR();
        if(xhr == null){
            option.error({ code: -1, msg: "no XHR" });
            return;
        }
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4){
                if(xhr.status >= 200 && xhr.status <= 300 || xhr.status == 304)
                    option.success(xhr.responseText);
                else 
                    option.error({ err: xhr.status, msg: "something wrong" });
            }
        };
        if(option.type == "get"){
            xhr.open(option.type, _ajax.makeUrl(option.url, option.data), option.async);
            xhr.send();
        }
        else {
            xhr.open(option.type, option.url, option.async);
            xhr.setRequestHeader("Content-Type", "Application/x-www-form-urlencoded");
            xhr.send(_ajax.formSerialize(option.data));
        }
    };
    //ajax.prototype
    oter.ajax.prototype = {
        defaults: {
            async: true,
            type: "get",
            url: " ",
            data: {},
            dataType: "XML",
            error: function(err){ throw new Error(err.msg); },
            success: function(data){}
        },
        XHR: function(){
            if(typeof XMLHttpRequest != "undefined") return new XMLHttpRequest();
            else if(typeof ActiveXObject != "undefined"){
                if(arguments.callee.ver) return new ActiveXObject(arguments.callee.ver);
                else{
                    var ver = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp"];
                    for(var i = 0, z = ver.lenght; i < z; i++){
                        try{
                            new ActiveXObject(ver[i]);
                            arguments.callee.ver = ver[i];
                            break;
                        }
                        catch(e){
                            continue;
                        }
                    }
                }
            }
            else return null;
        },
        makeUrl: function(url, data){
            if(!data) return url;
            url += url.indexOf("?") > 0 ? "&" : "?";
            for(var key in data) 
                url += encodeURIComponent(key) + "=" + encodeURIComponent(data[key]) + "&";
            if(url.length > 0) url = url.substr(0, url.length - 1);
            return url;
        },
        formSerialize: function(data){
            var va = "";
            for(var key in data) 
                va += encodeURIComponent(key) + "=" + encodeURIComponent(data[key]) + "&";
            if(va.length > 0) va = va.substr(0, va.length - 1);
            return va;
        }
    } 
    //serialize
    oter.serialize = function(select){
        var parts = {}, elem, option, ova;
        var form = document.getElementById(select);
        if(form == null) return "";
        for(var i = 0, z = form.elements.length; i < z; i++){
            elem = form.elements[i];
            switch(elem.type){
                case "select-one":
                case "select-multiple":
                    if(elem.name.length){
                        for(var n = 0, m = elem.options.length; n < m; n++){
                            option = elem.options[n];
                            ova = " ";
                            if(option.hasAttribute) ova = option.hasAttribute("value") ? option.value : option.text;
                            else ova = option.attributes["value"].specified ? option.value : option.text;
                            parts.push(encodeURIComponent(elem.name) + "=" + encodeURIComponent(ova))
                        }
                    }
                    break;
                case "undefined":
                case "file":
                case "submit":
                case "reset":
                case "button": break;
                case "radio":
                    if(!elem.checked) break;
                default: 
                    if(elem.name.length) parts.push(encodeURIComponent(elem.name) + "=" + encodeURIComponent(elem.value))
            }
        }
        return parts.join("&");
    }
    //extend
    oter.extend = function(source, target){
        var obj = {};
        for(var key in target){
                if(source[key] && target[key]) obj[key] = target[key];
            else obj[key] = source[key];
        }
        return obj;
    }
    //byte length
    oter.byteLength = function(str) {
        var cArr = str.match(oter.regex.extra._byte);
        return str.length + (cArr == null ? 0 : cArr.length);
    };
    //出让 $ Oter oter 对象
    oter.doler = function() {
        if (_$) window.$ = _$;
        if (_oter) window.oter = _oter;
        if (_Oter) window.Oter = _Oter;
        return this;
    };

    //object
    //prototype
    oter.fn = oter.prototype = {
        ver: "1.0",
        constructor: oter,
        push: oter.arr.push,
        length: this.length,
        splice: oter.arr.splice,
        serialize: function(){
            var parts = {}, elem, option, ova;
            if(this.elems == null || this.elems.length < 1) return "";
            for(var form in this.elems){
                for(var i = 0, z = form.elements.length; i < z; i++){
                    elem = form.elements[i];
                    switch(elem.type){
                        case "select-one":
                        case "select-multiple":
                            if(elem.name.length){
                                for(var n = 0, m = elem.options.length; n < m; n++){
                                    option = elem.options[n];
                                    ova = " ";
                                    if(option.hasAttribute) ova = option.hasAttribute("value") ? option.value : option.text;
                                    else ova = option.attributes["value"].specified ? option.value : option.text;
                                    parts.push(encodeURIComponent(elem.name) + "=" + encodeURIComponent(ova))
                                }
                            }
                            break;
                        case "undefined":
                        case "file":
                        case "submit":
                        case "reset":
                        case "button": break;
                        case "radio":
                            if(!elem.checked) break;
                        default: 
                            if(elem.name.length) parts.push(encodeURIComponent(elem.name) + "=" + encodeURIComponent(elem.value))
                    }
                }
            }
            return parts.join("&");
        }
    };
    //init
    var init = oter.fn.init = function(selector) {

    };
    oter.fn.init.prototype = oter.fn;
    oter.extend = oter.fn.extend = function(src, target){
        if(target == null) {
            target = src;
            src = this;
        }
        if(typeof target != "object") return src;
        for(var name in target) src[name] = target[name];
        return src;
    };
})(window);