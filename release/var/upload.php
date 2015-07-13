<?php
	if(count($_POST) > 0) echo serialize($_POST);
	else if(count($_GET) > 0) echo serialize($_GET);
	else echo "a:1:{s:3:\"key\";s:5:\"value\";}";
?>