<?php
require "settings.php";
try {
    $codb = ($onMySQL)?new PDO("mysql:charset=utf8;host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass):new PDO("sqlite:./database.db");
}
catch(PDOException $e) {
    $out[0]["status"] = 3;
    $out[0]["reason"] = "Database connection failed";
    $out[1] = $e->getMessage();
    exit();
}
?>