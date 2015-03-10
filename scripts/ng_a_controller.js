var app = angular.module("app");
app.controller("ngHeaderController", function($scope, web){
    $scope.types = [];
    $scope.sign = "What a loser";
    $scope.himg = "/images/headerimg.jpg";
    $scope.getTypes = function(){
        web.post("/var/types.php", {"action": "gettypes", "typepid": 0}, function(data){
            if(data.err == false) return;
            data.list.unshift({typeid: 0, typepid: 0, typeshow:1, typename: "全部", typesort: 0, typevalid: 1});
            data.list.push({typeid: -1, typepid: 0, typeshow:1, typename: "Says", typesort: 0, typevalid: 1});
            for(var i in data.list){
                if(i == 0) data.list[i].cur = 1;
                else data.list[i].cur = 0;
            }
            $scope.types = data.list;
        });
    };
    $scope.makeCur = function(t_id){
        for(var i in $scope.types){
            if($scope.types[i].typeid == t_id) $scope.types[i].cur = 1;
            else $scope.types[i].cur = 0;
        }
    };
    $scope.getTypes();
});