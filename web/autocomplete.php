<?php
// CoLWI v0.9.0
// AutoComplete JSON application
// (c) SimonOrJ, 2015-2016

// Request parameters:
// s = server
// a = source
// b = material

// Testing script
error_reporting(-1);ini_set('display_errors', 'On');

// Set header
header('Content-type:application/json;charset=utf-8');

// Get config for login check
$c = require "config.php";

// Login check
require "res/php/login.php";
$login = new Login($c);
if (!$login->permission(Login::PERM_LOOKUP)) {
    echo '["Login is required."]';
    exit();
}

// Check for required variables
if ($se = empty($_REQUEST['s']) || empty($_REQUEST['a'])) {
    echo '["'
        .($se ? "Server" : "source")
        .' is not defined"]';
    exit();
}

// Check if file/cache exists.
if (!file_exists($file = "cache"."/".$_REQUEST['s']."/".$_REQUEST['a'].".php")) {
    echo "[]";
    exit();
}

$sVal = $_REQUEST["b"]; //Keyword

// Get data source
$data = include $file;

// Material name validation
if($_REQUEST["a"] === "material"){
    $data = array_filter($data, function($v) {return((strpos($v,":") !== false));});//Remove non-colon words
    require "co2mc.php";
    $Cc = $translateCo2Mc?new co2mc():new keepCo();
    foreach($data as $k=>$v)$data[$k]=$cc->getMc($v);
}

$data=array_filter($data, function($v) use ($sVal) {return(stripos($v,$sVal) !== false);});//Keyword Filter
foreach($_REQUEST["e"] as $value) if(($key = array_search($value,$data)) !== false) unset($data[$key]);//Remove already listed words
function match($a,$b){global $sVal;return levenshtein($a,$sVal)>levenshtein($b,$sVal) ? 1 : -1;};//Sorting function
usort($data,"match");//Sort

echo json_encode(array_slice($data,0,$_REQUEST["l"]));
?>