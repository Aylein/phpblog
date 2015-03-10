var app = angular.module("app");
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