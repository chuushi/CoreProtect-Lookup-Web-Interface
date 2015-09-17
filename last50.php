<!DOCTYPE html>
<html>
<head>
<meta charset="latin1">
<title>Stuff</title>
</head>
<body>
<pre><?php

error_reporting(-1);
ini_set('display_errors', 'On');

require 'settings.php';
require 'cachectrl.php';



function chat() {
    global $mcsql;
    $result=$mcsql->query('SELECT `user`,`message` FROM co_chat ORDER BY time DESC limit 50;');
    
    while($row = $result->fetch_assoc()) {
        echo '&lt;'.cou($row['user']).'&gt; '.$row["message"].'<br>';
    }
}

function command() {
    global $mcsql;
    $result=$mcsql->query('SELECT `user`,`message` FROM co_command ORDER BY time DESC limit 50;');
    
    while($row = $result->fetch_assoc()) {
        echo '&lt;'.cou($row['user']).'&gt; '.$row["message"].'<br>';
    }
}

function block() {
    global $mcsql, $material, $entity;
    $result=$mcsql->query('SELECT `user`,`wid`,`x`,`y`,`z`,`type`,`action`,`rolled_back` FROM co_block ORDER BY time DESC limit 50;'); //missing: `meta`,`data`
    
    while($row = $result->fetch_assoc()) {
        echo cou($row['user']).' ';
        switch ($row['action']){
            case 0: echo 'destroyed';
            break;
            case 1: echo 'placed';
            break;
            case 2: echo 'clicked';
            break;
            case 3: echo 'killed';
        }
        echo ' ';
        
        if ($row['action'] == 3) echo $entity[$row['type']];
        else echo $material[$row['type']];
        
        echo ' at '.$row['x'].' '.$row['y'].' '.$row['z'].' .<br>';
    }
}



$start = microtime(true);

/*
$result=$mcsql->query('(SELECT `user`,`message`,`time` FROM co_command ORDER BY time DESC limit 50) UNION ALL (SELECT `user`,`message`,`time` FROM co_chat ORDER BY time DESC limit 50) ORDER BY time DESC limit 50;');

while($row = $result->fetch_assoc()) {
    echo '&lt;'.cou($row['user']).'&gt; '.$row["message"].'<br>';
}*/

echo 'Last fifty:<br><br>Chat<br>';
chat();
echo '<br><br>Commands<br>';
command();
echo '<br><br>Block/kill History<br>';
block();

printf("Time taken: %.6fs\n", microtime(true)-$start);

?></pre></body>
</html>
