var form = document.getElementById("sign_post");
form.onsubmit = function(){
    var val = form.admin_pass.value;
    var acu = form.admin_name.value;
    if(acu.trim) {
        val = val.trim();
        acu = acu.trim();
    }
    if(val != "" && acu != "") sub(acu, val);
    return false;
};
var sub = function(name, pass){
    $.ajax({
        type: "post",
        url: "var/action.php",
        data: {pass: pass, user: name, action: "signin"},
        dataType: "json",
        success: function(data){
            if(data.res == false){
                alert(data.msg);
                form.admin_pass.value = "";
                return;
            }
            if(data.code == "session") window.location = "/admin/index.php";
            else window.location = "/";
        }
    })
};
var signed = function(){
    $.ajax({
        type: "post",
        url: "var/action.php",
        data: {action: "issigned"},
        dataType: "json",
        success: function(data){
            if(data.res === true) window.location = "/admin/index.php";
        }
    })
};
signed();