<?php
/* Density-based pixel map creation
 * Created by SimonOrJ for CoreProtect.
 * Originally bundled with CoreProtect Web Lookup Interface.
 * Credit where its due.
 */
 
// coords: for x and z [0,1]. same for y if it exists.
// Get the coordinates and set them.
if(isset($_GET['r'])) {
    $x = [$_GET['x']-$_GET['r'],$_GET['x']+$_GET['r']];
    $z = [$_GET['x']-$_GET['r'],$_GET['z']+$_GET['r']];
    if (isset($_GET['y'])) $y = [$_GET['y']-$_GET['r'],$_GET['y']+$_GET['r']];
    else $y = [0,255];
}
else {
    $x = $_GET['x'];
    $y = $_GET['y'];
    $z = $_GET['z'];
}
?>