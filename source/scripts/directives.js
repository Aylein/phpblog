var app = angular.module("app");
app.directive("aoPress", function(){
    return {
        restrict: "A",
        link: function($scope, $elem){
            $elem.on("keydown", function(event){
                var $e = event || window.event;
                if($scope.press_callback)
                    $scope.press_callback($e, $elem);
            });
        }
    };
});
app.directive("ngPromp", function(dom, main){
    return {
        restrict: "E",
        replace: true,
        templateUrl: "/require/ngPromp.html",
        link: function($scope, $elem, $attrs){
            var config = {
                width: 200,
                height: 150,
                title: "提示",
                discript: "",
            };
            $scope.showPromp = function(op){
                dom.text($elem[0], "this is the updated promp div");
                op = main.extend(true, config, op);
                console.log(op);
            };
            $scope.tangoPromp = function(){
                dom.tango($elem[0]);
            };
        }
    };
});
app.directive("aoCs", function(dom, broswer, main, cache, extra){
    return {
        restrict: "E",
        replace: true,
        templateUrl: "/require/ngComment.html",
        link: function($scope, $elem, $attr){
            var sign = "__aoSc_" + extra.random(5) + "__";
            var aoCs = $scope.aoSc = {
                sc_ac_img: dom.get("sc_ac_img"),
                sc_ac_content: dom.get("sc_content"),
                sc_ac_show: dom.get("show")
            }, range = {};
            aoCs._tango = function(){
                if(aoCs.sc_ac_img.style.display == "block")
                    aoCs.sc_ac_img.style.display = "none";
                else aoCs.sc_ac_img.style.display = "block";
            };
            aoCs._img = function(src){
                document.execCommand('InsertImage', false, src);
                aoCs.sc_ac_img.style.display = "none";
            };
            aoCs._bold = function(){ document.execCommand("Bold"); };
            aoCs._italic = function(){ document.execCommand("Italic"); };
            aoCs._range = function(){
                if(window.getSelection) range = {s: window.getSelection(), f: "window"};
                else if(document.selection) range = {s: document.selection.createRange(), f: "document"};
                else if(aoCs.sc_ac_content.createTextRange) range = {s: aoCs.sc_ac_content.createTextRange(), f: "node"};
                if(range.s && range.s.getRangeAt) range.r = range.s.getRangeAt(0);
                else if(document.createRange){range.r = document.createRange(); range.f = "document";}
                range.sw = range.s.toString();
                //console.log(broswer.bs);
                //console.log(range);
            };
            aoCs._keydown = function(e){     
                console.log(e);
                if(aoCs.sc_ac_content.contentEditable != "true") return;
                if(e.repeat) return;
                else if(e.keyCode == 27) document.execCommand("undo"); //撤销
                else if(e.keyCode == 9){
                    document.execCommand("Indent"); //tab
                    dom.no(e);
                }
                else if(e.keyCode == 13 && e.ctrlKey) aoCs._send(); //control + enter
                else if(e.keyCode == 8 && e.ctrlKey) aoCs.sc_ac_content.innerHTML = ""; //control + backspace
            };
            aoCs._send = function(){
                //aoCs.sc_ac_show.innerHTML = aoCs.sc_ac_content.innerHTML;
            };
            var init = function(){
                //console.log(broswer.toString());
                $scope.press_callback = aoCs._keydown;
                aoCs.sc_ac = [], src = "images/ac/ac_", p = ".png";
                for(var i = 1; i <= 50; i++) aoCs.sc_ac.push(src + i + p);
            };
            init();
        }
    };
});