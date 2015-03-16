<?php

?>
<!DOCTYPE html>
<html>
<head>
    <title>test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <form action="var/comments.php" id="myform">
        <input type="text" id="value" name="value" />
        <input type="submit" value="yes" id="yes" />
    </form>
</body>
<script src="scripts/jquery-1.11.1.min.js"></script>
<script>
    $(function(){
        var va = $("#value");
        var fo = $("#myform");
        var test = function(str, callback){
            $.ajax({
                type: "post",
                url: "var/comments.php",
                data: {
                    action: "test",
                    pass: str
                },
                dataType: "json",
                success: callback
            });
        };
        fo.submit(function(){
            var str = prompt("输入密码");
            if(!str) return false;
            test(str, function(data){
                console.log(data);
            });
            return false;
        });
        va.focus();
    });
</script>
</html>