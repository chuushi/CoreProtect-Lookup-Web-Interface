<?php
require "settings.php"; // For login
$success=true;
foreach(["art","entity","material","user","world"] as $base){
    $file="./cache/".$base.".php";
    if(file_exists($file))if(!unlink($file))$success=false;
}
echo $success?"[true]":"[false]";
?>