<?php
/* CoreProtect LWI - v0.7.0-beta coded by SimonOrJ.
 * CoreProtect developed by Intellii.
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
    $dbhost = "127.0.0.1";
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
 *               Settings for index.php               *
\* ================================================== */

/* Datetime Picker and Date/Time Display format to use.
 * This uses moment's date display option.  Refer to 
   http://momentjs.com/docs/#/displaying/format/
 * for formatting help. */
$dateFormat = "ll";
$timeFormat = "LTS";


/* Dividor Time - How far apart should the time be before
 * a table time dividor kicks in? 
 * Time in milliseconds.*/
$timeDividor = 1200000; // Default: 20 minutes (1200000)


/* MC Dynamic Map link and settings
 * If there is no dynmap, leave it as false.
 * If you want to use dynmap to assist in block lookup,
 * toggle it true.*/
$useDynmap = false;
// URL to the dynmap
$dynmapURL = "http://127.0.0.1:8123/";
// Zoom Level
$dynmapZoom = 6; // Higher is closer.
// Map type
$dynmapMapName = "flat"; //flat, surface, or cave


// texturepack to use. Use _EXACT_ folder/directory name.
//$texture = "default"; //In Progress!

// More features to come...


/* ================================================== *\
 *             End of user configuration              *
\* ================================================== */
if(!$useDynmap) $dynmapURL = false;
?>
