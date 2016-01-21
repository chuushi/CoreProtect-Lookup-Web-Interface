<?php
/* CoreProtect Lookup Web Interface v0.5.0-alpha created by SimonOrJ.
 * CoreProtect developed by Intellii.
 * This is still in alpha.  Expect bugs here and there.

/* ================================================== *\
 *     Database and Related Settings for conn.php     *
\* ================================================== */

// Is the database on a mysql server?
// true if yes, false if no.
$onMySQL = false;

if($onMySQL == false) {
    // Provide the path to the database.sql file.
    $dbpath = "./database.db";
}
else { #if $sqlServer == true:
    // Fill out the following:
    // Hostname[:port]
    $dbhost = "localhost";
    // NySQL username
    $dbuser = "username";
    // MySQL password associated with username
    $dbpass = "password";
    // Database name
    $dbname = "minecraft";
}

// CoreProtect prefix (if you are using a custom prefix)
$co_ = "co_"; // Default: "co_"

// Minecraft Server-Webserver Time Offset in seconds
$timeOffset = 0;

/* ================================================== *\
 *          Auxiliary settings for index.php          *
\* ================================================== */

// texturepack to use. Use _EXACT_ folder/directory name.
$texture = "default"; //In Progress!

// Dynamic Map link for coordinates 
// (remove the # to activate)
#$dynmapURL = "http://localhost:8123/"


// More features to come...


/* ================================================== *\
 *             End of user configuration              *
\* ================================================== */
if($onMySQL) $codb = new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass);
else $codb = new PDO("sqlite:./database.db");
?>
