<?php
// COLWI v0.7.0-beta - AutoComplete Module
$sVal = $_REQUEST["b"];//Keyword
$data = include "cache/".$_REQUEST["a"].".php"; //Data source
if($_REQUEST["a"] === "material"){
    $data = array_filter($data, function($v) {return((strpos($v,":") !== false));});//Remove non-colon words
    include "settings.php";
    require "co2mc.php";
    $cc = $translateCo2Mc?new co2mc():new keepCo();
    foreach($data as $k=>$v)$data[$k]=$cc->getMc($v);
}
$data=array_filter($data, function($v) use ($sVal) {return(stripos($v,$sVal) !== false);});//Keyword Filter
foreach($_REQUEST["e"] as $value) if(($key = array_search($value,$data)) !== false) unset($data[$key]);//Remove already listed words
function match($a,$b){global $sVal;return levenshtein($a,$sVal)>levenshtein($b,$sVal) ? 1 : -1;};//Sorting function
usort($data,"match");//Sort
echo json_encode(array_slice($data,0,$_REQUEST["l"]));
?>