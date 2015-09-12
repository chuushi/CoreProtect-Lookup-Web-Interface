<?php

require 'settings.php';

// Function to reset cache
function clCache() {
    global $mcsql;

    // Reset users cache
    $fp = fopen('cache/user.php','w');
    fwrite($fp, '<?php $'.$co_prefix.'user=array(
)?>'); 
    fclose($fp);

    // List the maps to cache
    $type = array('art','entity','material');
    $content = '<?php ';
    for ($n = 0; $n <= 2; $n++) {
        $sql = $mcsql->query('SELECT `id`,`'.$type[$n].'` FROM '.$co_prefix.$type[$n].'_map;');
        $content .= '$'.$type[$n].'=array(';
        while ($row = $sql->fetch_assoc()) $content .= $row['id']."=>'".$row[$type[$n]]."',";
        $content .= ');';
    }
    $sql = $mcsql->query('SELECT `id`,`world` FROM '.$co_prefix.'world;');
    $content .= '$world=array(';
    while ($row = $sql->fetch_assoc()) $content .= $row['id']."=>'".$row['world']."',";
    $content .= ')';
    $content .= '?>';
    
    // Write the data fo file
    $fp = fopen('cache/map.php','w');
    fwrite($fp, $content); 
    fclose($fp); 
}

if (!file_exists('cache/user.php') || !file_exists('cache/map.php')) clCache();
require 'cache/user.php';
require 'cache/map.php';

// Function to update username to cache
function userUpdate($value,$isId) {
    global $co_user,$mcsql;
    if ($isId) $key = 'rowid';
    else $key = 'user';
    $sql = $mcsql->query('SELECT `rowid`,`user` FROM '.$co_prefix.'user WHERE '.$key.' = "'.$value.'";')->fetch_assoc();
    if (empty($sql)) return 1;
    // Define the missing id
    $co_user[$sql['rowid']] = $sql['user'];
    
    // load the data and delete the line from the array 
    $lines = file('cache/user.php'); 
    $last = sizeof($lines) - 1;
    $lines[$last] = $sql['rowid']." => '".$sql['user']."',\n)?>";
    
    // write the new data to the file 
    $fp = fopen('cache/user.php', 'w');
    fwrite($fp, implode('', $lines)); 
    fclose($fp); 
}

// Function for id to username retrieval
function cou($id) {
    global $co_user;
    if (!array_key_exists($id,$co_user)) userUpdate($id,1);
    return $co_user[$id];
}

// Function for username to id retrieval
function user2id($user) {
    global $co_user;
    if (!array_search(strtolower($user),array_map('strtolower',$co_user),true)) if (userUpdate($user,0)) return 0;
    return array_search(strtolower($user),array_map('strtolower',$co_user),true);
}

// Function for id to art/entity/material map or world retrieval
function comap($map,$value) {
    global $art,$entity,$material,$world;
    return array_search($value,$$map,true);
}

// Function for art/entity/material map to id retrieval
function map2id($map,$value) {
    global $art,$entity,$material,$world;
    if (!$ret = array_search(strtolower($value),array_map('strtolower',$$map),true)) return 0;
    return $ret;
}
?>