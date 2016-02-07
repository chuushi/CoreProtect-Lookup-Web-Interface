<?php
// COLWI Version v0.6.0 - AutoComplete Module
/* This uses the data from cache to bring up suggestions for
 * user
 * material
 * world
 * ... entity?
 */
$search_text = $_REQUEST["b"]; // word to look up
$data = include "cache/".$_REQUEST["a"].".php";
if($_REQUEST["a"] === "material") $data = array_filter($data, function($v) {return((strpos($v,":") !== false));});
foreach($_REQUEST["e"] as $value) if(($key = array_search($value, $data)) !== false) {
    unset($data[$key]);
}
$data = array_slice(array_filter($data, function($v) use ($search_text) {return(stripos($v,$search_text) !== false);}),0,$_REQUEST["l"]);
echo json_encode($data);
?>