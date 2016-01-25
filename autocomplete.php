<?php
// COLWI Version v0.6.0 - AutoComplete Module
/* This uses the data from cache to bring up suggestions for
 * user
 * material
 * world
 * ... entity?
 */
$_GET["a"]; // what to look up
$search_text = $_GET["b"]; // word to look up
$data = include "cache/".$_GET["a"].".php";
if($_GET["a"] === "material") $data = array_filter($data, function($v) {return((strpos($v,":") !== false));});
$data = array_filter($data, function($v) use ($search_text) {return(stripos($v,$search_text) !== false);});
echo json_encode($data);
?>