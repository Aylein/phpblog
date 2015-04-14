var app = angular.module("app");
app.directive("ngPress", function(){
    return {
        restrict: "A",
        link: function($scope, $elem){
            $elem.on("keydown", function(event){
                var $e = event || window.event;
                if($scope.press_callback){
                    if($scope.press_callback($e, $elem)){
                        if($e.preventDefault) $e.preventDefault();
                        else window.event.returnValue = false;
                    }
                }
            });
        }
    };
});
app.directive("ngPromp", function(main, extra){
    return {
        restrict: "E",
        replace: true,
        templateUrl: "/require/ngPromp.html",
        link: function($scope, $elem, $attrs){
            var Default = function(){
                this.width = 200;
                this.height = 150;
                this.title = "提示";
                this.discript = "";
            };
            $scope.showPromp = function(option){
                main.text($elem[0], "this is the updated promp div");
                option = extra.extend(new Default(), option);
                console.log(option);
            };
            $scope.tangoPromp = function(){
                main.tango($elem[0]);
            };
        }
    };
});
app.directive("aoSc", function(main){
    return {
        restrict: "E",
        replace: true,
        templateUrl: "/require/ngComment.html",
        link: function($scope, $elem, $attr){
            var aoCs = $scope.aoSc = {};
            aoCs.sc_ac_img = main.get("sc_ac_img");
            aoCs.sc_ac_content = main.get("sc_content");
            //aoCs.sc_ac_range;
            aoCs._tango = function(){
                aoCs.sc_ac_content.focus();
                if(aoCs.sc_ac_img.style.display == "block")
                    aoCs.sc_ac_img.style.display = "none";
                else aoCs.sc_ac_img.style.display = "block";
            };
            aoCs._img = function(src){
                aoCs.sc_ac_content.focus();
                document.execCommand('InsertImage', false, src);
                aoCs.sc_ac_img.style.display = "none";
            };
            aoCs._bold = function(){
                aoCs.sc_ac_content.focus();
                document.execCommand("Bold");
            };
            aoCs._range = function(){
                /*
                if(aoCs.sc_ac_img.selectionStart)
                    console.log(aoCs.sc_ac_img.selectionStart());
                else{
                    var range = document.selection.createRange();
                    range.moveStart("character",-aoCs.sc_ac_img.value.length);
                    console.log(range.text.length);
                }
                */
                /*
                var select;
                if(document.selection) select = document.selection.createRange();
                else if(window.getSelection) select = window.getSelection();
                else if(document.getSelection) select = document.getSelection();
                console.log(select.toString());
                */
            };
            var sinit = function(){
                aoCs.sc_ac = [], src = "images/ac/ac_", p = ".png";
                for(var i = 1; i <= 50; i++) aoCs.sc_ac.push(src + i + p);
            };
            sinit();
        }
    };
});
app.directive("ngHtype", function(){
    return {
        restrict: "A",
        replace: false,
        templateUrl: "/require/ngHeader.html",
        link: function(scope, elem, attrs){
            /*
            scope.makeHead = function(t_id){
                scope.$apply(function(){
                    for(var i in scope.types){
                        if(scope.types[i].typeid == t_id) scope.types[i].cur = 1;
                        else scope.types[i].cur = 0;
                    }
                });
            };
            */
            elem.on("click", function(event){
                var el = event.target;
                if(el.nodeName != "A") return false;
                var t_id = el.getAttribute("t_id");
                scope.$apply(function(){
                    for(var i in scope.types){
                        if(scope.types[i].typeid == t_id) scope.types[i].cur = 1;
                        else scope.types[i].cur = 0;
                    }
                });
            });
        }
    };
});