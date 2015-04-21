<?php
include_once("Entity.php");
include_once("Main.php");
class Sign{
    var $signid; // int primary key auto_increment,
    var $signname; // nvarchar(20) not null,
    var $signcreatetime; // timestamp default current_timestamp,
    var $userid; // int not null,
    var $signsort; // int default 0,
    var $signvalid; // int default 1

    
}
?>