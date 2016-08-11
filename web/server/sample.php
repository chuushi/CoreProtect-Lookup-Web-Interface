<?php return array(
  // If you want to configure server databases on your own, you can duplicate
  //   this file and write some configuration.
  
  // If you set things up on the web UI and want to input passwords manually,
  //   you will have to visit this directory and configure the password.
  'db' =>
  array (
    'type' => 'sqlite',         // Type of database: mysql or sqlite
    'host' => '127.0.0.1',      // mysql; server IP/hostname
    'user' => 'username',       // mysql; username
    'pass' => 'password',       // mysql; password
    'data' => 'coreprotect',    // mysql; Database
    'path' => '../database.db', // sqlite; Relative to webroot
  ),
  'co'     => 'co_',            // Prefix.
  'legacy' => false,            // Did this start logging below CP v2.11?
  'dynmap' =>                   // If no dynmap, erase/comment out the array
                                //   below and set this to false.
  array(
    'link' => 'http://127.0.0.1:8123/', // Dynmap link
    'zoom' => 6,                        // Zoom
    'map'  => 'flat',                   // Available: flat, surface, or cave
  ),
)?>
