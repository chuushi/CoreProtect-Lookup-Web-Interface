<?php
/* CoreProtect LWI - v0.8.2-beta coded by SimonOrJ.
 * This will be completely different on v0.9.0.
 * CoreProtect developed by Intellii.
/* ================================================== *\
 *                   Login Settings                   *
\* ================================================== */
$_login = [
// Require logon? true if yes, false if no.
"required" => true,
// Username
"username" => "admin",
// Password
"password" => "password",
// Duration to stay logged in if "Remember me" is checked
"duration" => 21,
// Login was made possible through richcheting's script.
];

/* ================================================== *\
 *     Database and Related Settings for conn.php     *
\* ================================================== */
$_sql = [
// Is the database on a mysql server?
// true if yes, false if no.
"onMySQL" => false,

// If using SQLite:
  // Provide the path to the database.sql file.
  "databasePath" => "../database.db",

// If using MySQL:
  // Hostname[:port]
  "hostname" => "127.0.0.1",
  // NySQL username
  "username" => "username",
  // MySQL password associated with username
  "password" => "password",
  // Database name
  "database" => "minecraft",
];
// CoreProtect prefix (if you are using a custom prefix)
$co_ = "co_"; // Default: "co_"


/* Random string to encrypt SQL and prevent SQL injection.
 * Just type in some random string.  You'll never need to
 * memorize it. */
$SQL_key = "SomeStringToPreventSQLInjection(CHANGE ME)";


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
$_index=[
/* Copyright notice thing on the bottom of the page; I
 * dunno o.o
 * You can use "." to append to strings, and 'date("Y")'
 * to set current year.
 */
"copyright" => "SimonOrJ, 2015-".date("Y"),


"navigation" => [
/* Navbar links
 * The links are made in this format:
 "[link label]" => "[link href]",
 * This is expandable, but be careful of how long you
 * make the navbar by using long link label names.
 */
"Home" => "/",
],

/* Datetime Picker and Date/Time Display format to use.
 * This uses moment's date display option.  Refer to 
   http://momentjs.com/docs/#/displaying/format/
 * for formatting help. */
"dateFormat" => "ll",
"timeFormat" => "LTS",


/* Dividor Time - How far apart should the time be before
 * a table time dividor kicks in? 
 * Time in seconds.*/
"timeDividor" => 300, // Default: 5 minutes (300)

// Intervals on which to create page links on navbar.
"pageInteval" => 25,


/* MC Dynamic Map link and settings
 * If there is no dynmap, leave it as false.
 * If you want to use dynmap to assist in block lookup,
 * toggle it true.*/
"useDynmap"     => false,
// URL to the dynmap
"dynmapURL"     => "http://127.0.0.1:8123/",
// Zoom Level
"dynmapZoom"    => 6, // Higher is closer.
// Map type
"dynmapMapName" => "flat", //flat, surface, or cave
];

/* ================================================== *\
 *             End of user configuration              *
\* ================================================== */
if(!$_index["useDynmap"]) $_index["dynmapURL"] = false;
if($_login["required"]){session_start();require"res/login.php";$login = new Login;$login->authorize();}
?>
