var app = angular.module("app");
app.controller("ngMainController", function($scope, $rootScope, $location, $route, web){
    $scope.clearCur = function(){
        $rootScope.sign.all.cur = 0;
        $rootScope.sign.say.cur = 0;
        $rootScope.sign.about.cur = 0;
        for(var i in $rootScope.sign.types)
            $rootScope.sign.types[i].cur = 0;
    };
    $scope.click = function(t_id){
        $scope.clearCur();
        switch(t_id){
            case "all": $location.path("/index"); break;
            case "say": $location.path("/says"); break;
            case "about": $location.path("/about"); break;
            default:
                if($rootScope.sign.types["t_" + t_id])
                    $location.path("/found/" + t_id);
                break;
        };
    };
    $scope.makeCur = function(){
        $scope.clearCur();
        var path = $location.path(), typeid = $route.current.params.typeid;
        if(path.match(/^\/found\/\d+$/)) {
            if($rootScope.sign.types["t_" + typeid])
                $rootScope.sign.types["t_" + typeid].cur = 1;
        }
        else if(path == "/index") $rootScope.sign.all.cur = 1;
        else if(path == "/says") $rootScope.sign.say.cur = 1;
        else if(path == "/about") $rootScope.sign.about.cur = 1;
    };
    var init = function(){
        $rootScope.sign.types = {};
        $rootScope.sign.sign = "What a loser";
        $rootScope.sign.himg = "/images/headerimg.jpg";
        $rootScope.sign.all = {cur: 0, typeid: "all", typename: "全部"};
        $rootScope.sign.say = {cur: 0, typeid: "say", typename: "Says"};
        $rootScope.sign.about = {cur: 0, typeid: "about", typename: "About"};
        web.post("/var/action.php", {"action": "gettypes", "typepid": 0}, function(data){
            if(data.err == false) return;
            for(var i = 0, z = data.list.length; i < z; i++){
                data.list[i].cur = 0;
                $rootScope.sign.types["t_" + data.list[i].typeid] = data.list[i];
            }
            $scope.makeCur("all");
            $scope.$on("$locationChangeSuccess", $scope.makeCur);
        });
    };
    if($rootScope.sign) return;
    $rootScope.sign = {};
    init();
});
app.controller("urlController", function($scope, web, $location, $routeParams){
    var path = $location.path(), typeid = $routeParams.typeid;
});
app.controller("saysController", function($scope, web){
    var content = document.getElementById("content");
    $scope.load = function(){

    };
    $scope._bold = function(){
        document.execCommand("Bold");
    };
    $scope._italic = function(){
        document.execCommand("Italic");
    };
    $scope._line = function(){
        document.execCommand("Underline");
    };
    $scope._clear = function(){
        content.innerHTML = "";
        content.focus();
    };
    $scope._send = function(){
        content.contentEditable = false;
        console.log(content.innerHTML);
        setTimeout($scope.load, 2000);
    };
    $scope.press_callback = function(e, elem){
        //console.log(e.keyCode);
        if(e.repeat) return;
        else if(e.keyCode == 27) document.execCommand("undo"); //撤销
        else if(e.keyCode == 9){
            document.execCommand("Indent"); //tab
            return true;
        }
        //else if(e.keyCode == 65 && e.ctrKey){
        //    document.execCommand("SelectAll"); //control + a
        //    return true;
        //}
        else if(e.keyCode == 13 && e.ctrlKey) $scope._send(); //control + enter
        else if(e.keyCode == 8 && e.ctrlKey) content.innerHTML = ""; //control + backspace
    };
    var init = function(){
        $scope.ac = [], src = "images/ac/ac_", p = ".png";
        for(var i = 1; i <= 50; i++) $scope.ac.push(src + i + p);
    }
    init();
});