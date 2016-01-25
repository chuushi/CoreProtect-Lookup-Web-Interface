<?php
require "settings.php";
$codb = ($onMySQL)?new PDO("mysql:charset=utf8;host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass):new PDO("sqlite:./database.db");
?>