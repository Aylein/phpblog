<?php
    include_once("lib/Main.php");
    include_once("lib/Entity.php");
    include_once("lib/User.php");
    //phpinfo();
    //exit();

    $ip = Commen::GetIP();
    print_r($ip);
    echo "<br />";

    $t = time();
    echo date("Y-m-d")."<br />";
    echo $t."<br />";
    echo strtotime(date('Y-m-d H:i:s',strtotime("+ 1 year")))."<br />";

    if(isset($_COOKIE["ao"])){
        print_r($_COOKIE["ao"]);
        echo "<br />";
    }
    else{
        setCookie("ao", Commen::UUID());
        echo "<br />";
    }

    echo uniqid(rand(), true)."<br />";
    echo "<br />";
    echo ord("a")."<br />";
    echo ord("z")."<br />";
    echo ord("A")."<br />";
    echo ord("Z")."<br />";
    echo "<br />";
    $now = date("H");
    echo chr($now + 65).chr($now + 97)."<br />";

    echo "<br />";
    echo Commen::Rand(3)."#".Commen::Rand(5).".".chr($now + 65).chr($now + 97);
    echo "<br />";

    /*
    $str = "insert into Main (_key, _value) values (:_key, :_value); select * from Main where id = @@IDENTITY;";
    $paras = Array(":_key" => $t, ":_value" => "1");
    $en = new Entity();
    $res = $en->Querys($str, $paras);
    print_r($res);
    echo "<br />";
    //if($res) print_r($res);
    //echo "faild";
    $str = "select * from Main; select * from Main where id = 1;";
    $en = new Entity();
    $res = $en->Querys($str, null);
    print_r($res);
    echo "<br />";
    */

    ///*
    $user = new User();
    $user->username = "AyleinOter";
    $user->userpass = md5("mm19880209");
    $user->userimg = "/images/headerimg.jpg";
    $user->usersort = 0;
    $user->uservalid = 1;
    $res = User::Add($user);
    print_r($res);
    //*/

    /*
    $user = User::Get(10086);
    echo "<br />";
    print_r($user);
    $user->username = "ayyyy";
    echo "<br />";
    if(User::Update($user)) echo "yes";
    else echo "no";
    echo "<br />";
    print_r($user);
    */
?>