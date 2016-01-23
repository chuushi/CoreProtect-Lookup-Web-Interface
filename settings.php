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
elseif ($onMySQL == true) {
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
$timeOffset = 0; // accepts negative values, Default: 0

/* Legacy Database Block Search Support
 * Is your database upgraded from CoreProtect verson 2.10?
 * This is important if you want to look up block data
 * that were recorded before you updated CoreProtect above
 * version 2.10.x.  This will cause block search error if
 * your database is first setup by CoreProtect 2.11 and
 * above.
 
 * If you want to lookup recorded data before you updated
 * Coreprotect to 2.11, then toggle it true.  If the
 * database was setup by CoreProtect 2.11 or above, or if 
 * you don't care about the old data, toggle it false. */
$legacySupport = false;

/* Block Name Conversion
 * CoreProtect likes to use legacy Minecraft block names,
 * making it confusing for some people to make a proper
 * block lookup query when they want to look up changes
 * made to, for example, a wooden plank.  Current
 * Minecraft name for a wooden plank is "minecraft:plank",
 * but CoreProtect likes to use "minecraft:wood" instead.
 
 * If you want to see search results or make search query
 * by the current Minecraft name, leave below as "true".
 * Otherwise, toggle it "false". */
$translateCo2Mc = true;


/* ================================================== *\
 *          Auxiliary settings for index.php          *
\* ================================================== */

// texturepack to use. Use _EXACT_ folder/directory name.
//$texture = "default"; //In Progress!

// Dynamic Map link for coordinates 
// (remove the # to activate)
#$dynmapURL = "http://localhost:8123/"


// More features to come...


/* ================================================== *\
 *             End of user configuration              *
\* ================================================== */
$codb = ($onMySQL)?new PDO("mysql:charset=utf8;host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass):new PDO("sqlite:./database.db");
?>
