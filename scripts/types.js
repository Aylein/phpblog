/*
function ffcController($scope){
    $.ajax({
        type: "post",
        url: "../var/types.php",
        data: {action: "gettypes"},
        dataType: "json",
        success: function(data){
            if(data.err) return;
            $scope.p = data.list;
        }
    });
}
ffcController();
*/
function typesController($scope){
    $scope.q = [{typeid: 1, typename: "22"}, {typeid: 2, typename: "33"}];
}