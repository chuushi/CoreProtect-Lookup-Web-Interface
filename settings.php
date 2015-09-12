<?php
// CoreProtect Lookup Web Interface created by SimonOrJ.
// CoreProtect developed by Intellii.
/* --------------------------------
    Database Settings for conn.php
   --------------------------------*/
// Hostname
$dbhost = "localhost";
// NySQL username
$dbuser = "username";
// MySQL password associated with username
$dbpass = "password";
// Database name
$dbname = "minecraft";
// CoreProtect prefix (if you have custom prefix) Default: co_
$co_prefix = "co_:;

/* ----------------------------------
    Auxiliary settings for index.php
   ----------------------------------*/
// Dynamic Map link (Optional, uncomment to activate)
//$dynmapURL = "http://localhost:8123/"
// More features to come...

/* ---------------------------
    End of user configuration
   ---------------------------*/

$mcsql = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
?>
