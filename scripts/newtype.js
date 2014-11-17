$(function(){
    var typename = function(){
        var va = $.trim($("#typename").val());
        var hid = $("#typename_hid");
        var bo = true;
        if(va == "") { hid.text("*填写类型名称"); bo = false; }
        else if(va.length > 15) { hid.text("*类型名称为 1 - 15 个字符"); bo = false; }
        else hid.text("");
        return bo;
    };
    var typesort = function(){
        var va = $.trim($("#typesort").val());
        var hid = $("#typesort_hid");
        var bo = true;
        if(!va.match(regex.isNumber)) { hid.text("*排列顺序为数字"); bo = false; }
        else hid.text("");
        return bo;
    };
    $("#typename").blur(typename);
    $("#typesort").blur(typesort);
    $("#yes_bt").click(function(){
        var bo = true;
        bo = bo && typename();
        bo = bo && typesort();
        if(bo){
            var typeid_v = $("#typeid").val();
            var typepid_v = $("#typepid").val();
            var typeshow_v = $("#typeshow").val();
            var typename_v = $.trim($("#typename").val());
            var typesort_v = $.trim($("#typesort").val());
            var typevalid_v = $("#typevalid").val();
            $.ajax({
                type: "post",
                url: "/var/adminaction.php",
                data: { 
                    typeid: typeid_v,
                    typepid: typepid_v,
                    typeshow: typeshow_v,
                    typename: typename_v,
                    typesort: typesort_v,
                    typevalid: typevalid_v,
                    action: "newtype",
                },
                dataType: "Json",
                success: function(data){
                    alert(data.msg);
                    if(data.err){
                        return;
                    }
                    window.location = "/admin/types.php";
                }
            });
        }
        return false;
    });
});