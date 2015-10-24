<?php
// PHP code by SimonOrJ.  All Rights Reserved.
/* GET outputs will be:
- action (a)
- user (u)
- radius (r)
- query limit (lim)
- time(t) in seconds and t2
- block(b)
- chat/command/sign search (keyword)
- exclude (e) players or blocks.
- center/first coordinate (xyz)
- second coordinate (xyz2)
- Compile in json? (json)

Database tables to use:
block (a: block, click, kill)
entity (for mob kills) (use with block table)
sign (with block>data minecraft:sign and minecraft:wall_sign)
skull (with block>data minecraft:skull)
container (a: container) (maybe use with container blocks)
chat (a: chat)
command (a: command)
session (a:session)
username_log (a:username)

Output object:
out[0]:
    ['success'] 0 or 1
    ['err'] block, username, username and block, invalid query, and no results
*/
error_reporting(-1);
ini_set('display_errors', 'On');


require "settings.php";
require 'cachectrl.php';

// Record start time
$timer = microtime(true);

// convert GET data to PHP variables and array
// Things to convert ot array: a, u, b, keyword, e
while ($key = key($_GET)) {
    $get = current($_GET);
    if (in_array($key,array('a','b','e','eu','u','xyz','xyz2'),true)) $$key = explode(',', $get);
    elseif (in_array($key,array('r','lim','date','keyword','offset','showqry'),true)) $$key = $get;
    next($_GET);
}

// If action is empty
if (empty($a)) $a = array('block');
elseif ($a[0] == 'all') $a = array('block','chat','click','command','container','kill','session','username');

// If limit is empty
if (empty($lim)) $lim = 30;

// If offset is empty
if (empty($offset)) $find = $lim;
else $find = $lim+$offset;

// Compile suffix
$cmsuffix = ' ORDER BY time DESC LIMIT '.$find;

// Coordinate Conversion
if (isset($xyz) && (isset($r) || isset($xyz2))) {
    if (isset($r)) {
        $x = array($xyz[0]-$r,$xyz[0]+$r);
        $y = array($xyz[1]-$r,$xyz[1]+$r);
        $z = array($xyz[2]-$r,$xyz[2]+$r);
    }
    elseif (isset($xyz2)) {
        $x = array(min($xyz[0],$xyz2[0]),max($xyz[0],$xyz2[0]));
        $y = array(min($xyz[1],$xyz2[1]),max($xyz[1],$xyz2[1]));
        $z = array(min($xyz[2],$xyz2[2]),max($xyz[2],$xyz2[2]));
    }
    $coord = '(x BETWEEN '.$x[0].' AND '.$x[1].') AND (y BETWEEN '.$y[0].' AND '.$y[1].') AND (z BETWEEN '.$z[0].' AND '.$z[1].')';
}
else $coord = false;
    
// Date conversion
if (isset($date)) {
    $d = 'time<='.$date;
}
else $d = false;

// User conversion
$erru = [];
if (isset($u) || isset($eu)) {
    $uset = isset($u);
    $euset = isset($eu);
    if (in_array('username',$a,true)) {
        if ($uset) $usr = "user IN ('".implode("','",$u)."')";
        if ($euset) $eusr = "user NOT IN ('".implode("','",$eu)."')";
    }
    if ($uset) {
        foreach ($u as $key => $su) {
            if (!$u[$key] = user2id($su)) $erru[] = $su;
        }
        $uid = "user IN ('".implode("','",$u)."')";
    }
    else $uid = false;
    if ($euset) {
        foreach ($eu as $key => $su) {
            if (!$eu[$key] = user2id($su)) $erru[] = $su;
        }
        $euid = "user NOT IN ('".implode("','",$eu)."')";
    }
    else $euid = false;
}
else $uid = $euid = false;

// Block conversion
$errb = [];
if (isset($b) || isset($e)) {
    if (isset($b)) {
        foreach ($b as $key => $sb) {
            if (!$b[$key] = map2id('material',$sb)) $errb[] = $sb;
        }
        $bid = "type IN ('".implode("','",$b)."')";
    }
    else $bid = false;
    if (isset($e)) {
        foreach ($e as $key => $sb) {
            if (!$e[$key] = map2id('material',$sb)) $errb[] = $sb;
        }
        $eid = "type NOT IN ('".implode("','",$e)."')";
    }
    else $eid = false;
}
else $bid = $eid = false;


// Keyword into array
if (isset($keyword)) {
    $kwd = " REGEXP (REPLACE('".$keyword."',' ','|'))";
    $cwords = "message".$kwd;
//    $swords = 'MATCH (line_1,line_2,line_3,line_4)'.$kwd;
    $wordsearch = true;
}
else $wordsearch = false;


// SQL WHERE compling function
function where($vars) {
	global $bid,$eid,$uid,$euid,$usr,$eusr,$d,$r,$coord,$cwords,$swords,$wordsearch;
    $pt = array();
    if ($coord && in_array('coord',$vars,true)) array_push($pt,$coord);
    if ($d) array_push($pt,$d);
    if (in_array('block',$vars,true)) {
        foreach ([$bid,$eid] as $val) {
            if ($val) array_push($pt,$val);
        }
    }
    if ($uid) {
        if (in_array('uAsIs',$vars,true)) array_push($pt,$usr);
        else array_push($pt,$uid);
    }
    if ($euid) {
        if (in_array('uAsIs',$vars,true)) array_push($pt,$eusr);
        else array_push($pt,$euid);
    }
    if (in_array('wsearch',$vars,true) && $wordsearch) array_push($pt,$cwords);
    
    if (empty($pt)) $where = '';
    else {
        if (!in_array('noWhere',$vars,true)) $where = ' WHERE ';
        else $where = ' AND ';
        $where .= implode(' AND ',$pt);
    }
	return $where;
}

// Username or Block error reporting if there is any error
if ($erru || $errb) {
    $status['status'] = 1;
    if ($baddu = !empty($erru)) {
        $status['err'] = 'username';
        $status['username'] = $erru;
    }
    if (!empty($errb)) {
        if ($baddu) $status['err'] = 'username and block';
        else $status['err'] = 'block';
        $status['block'] = $errb;
    }
    echo json_encode([$status,[]]);
    exit;
}



// Assume both + and - state of action is not used (eg) +block and -block

// For block, kill, or click history
$sql = array();
if (in_array('block',$a,true) || in_array('kill',$a,true) || in_array('click',$a,true)) {
	$bla = [];
    $wherels = [];
	if ($kill = in_array('kill',$a,true)) {
        if (!($bid || $eid)) array_push($bla,"action=3");
    }// selects killing action
	if (in_array('block',$a,true)) array_push($bla,"action=0 OR action=1"); // selects block history
	if (in_array('click',$a,true)) array_push($bla,"action=2"); // selects clicking action
    if (($bid || $eid) && $kill) {
        if (!empty($bla)) array_push($sql,"SELECT `time`,'block' AS dbtable,`user`,`wid`,`x`,`y`,`z`,`type`,`data`,'1' AS amount,`action`,`rolled_back` FROM ".$co_prefix."block WHERE (".implode(' OR ',$bla).')'.where(['coord','block','noWhere']).$cmsuffix);
        array_push($sql,"SELECT `time`,'block' AS dbtable,`user`,`wid`,`x`,`y`,`z`,`type`,`data`,'1' AS amount,`action`,`rolled_back` FROM ".$co_prefix."block WHERE action=3".where(['coord','noWhere']).$cmsuffix);
    }
    else array_push($sql,"SELECT `time`,'block' AS dbtable,`user`,`wid`,`x`,`y`,`z`,`type`,`data`,'1' AS amount,`action`,`rolled_back` FROM ".$co_prefix."block WHERE (".implode(' OR ',$bla).')'.where(['coord','block','noWhere']).$cmsuffix);

}

// For chat or command history
$run = array();
if (in_array('chat',$a,true)) array_push($run,'chat');
if (in_array('command',$a,true)) array_push($run,'command');
if (!empty($run)) {
    foreach ($run as $val) array_push($sql,"SELECT `time`,'".$val."' AS dbtable,`user`,NULL as wid,NULL AS x,NULL AS y,NULL AS z,NULL AS type,`message` AS data,NULL AS amount,NULL AS action,NULL AS rolled_back FROM ".$co_prefix.$val.where(['wsearch']).$cmsuffix);
}

// For container history
if (in_array('container',$a,true)) array_push($sql,"SELECT `time`,'container' as dbtable,`user`,`wid`,`x`,`y`,`z`,`type`,`data`,`amount`,`action`,`rolled_back` FROM ".$co_prefix."container".where(['block','coord']).$cmsuffix);

// For session history
if (in_array('session',$a,true)) array_push($sql,"SELECT `time`,'session' AS dbtable,`user`,`wid`,`x`,`y`,`z`,NULL AS type,NULL AS data,NULL AS amount,`action`,NULL AS rolled_back FROM ".$co_prefix."session".where(['coord']).$cmsuffix);

// For username history
if (in_array('username',$a,true)) array_push($sql,"SELECT `time`,'username' AS dbtable,`user`,NULL as wid,NULL AS x,NULL AS y,NULL AS z,NULL AS type,`uuid` AS data,NULL AS amount,NULL AS action,NULL AS rolled_back FROM ".$co_prefix."username_log ".where(['uAsIs']).$cmsuffix);

// If offset exists
if (!empty($offset)) $cmsuffix = ' ORDER BY time DESC LIMIT '.$lim.' OFFSET '.$offset;

if (count($sql) == 1) $sql = substr($sql[0], 0, strpos($sql[0], "ORDER BY time DESC LIMIT ")).$cmsuffix.';';
else $sql = '('.implode(') UNION ALL (',$sql).')'.$cmsuffix.';';

/* Table columns:
'time', 'dbtable', 'user', 'wid', 'x', 'y', 'z', 'type', 'data', 'amount', 'action', 'rolled_back'

a inputs:
'block','chat','click','command','container','kill','session','username'

block, click, kill: time, dbtable, user, wid, x, y, z, type, data, amount, action (always 1), rolled_back
chat, command: time, dbtalbe, user, data
container: time, dbtable, user, wid, x, y, z, type, data, amount, action, rolled_back
session: time, dbtable, user, wid, x, y, z, action
username: time, dbtable, user, data
*/

$mcsql->set_charset('utf8');
$result = $mcsql->query($sql);


$return = [];

if ($mcsql->errno) {
    $status['status'] = 1;
    $status['err'] = 'invalid query';
    $status['sqlerr'] = [$mcsql->errno,$mcsql->error];
}
else {
    if (($numrows = $result->num_rows) === 0) {
        $status['status'] = 1;
        $status['err'] = 'no results';
    }
    else {
        $status['status'] = 0;
        $status['rows'] = $numrows;
        // The Master Code.  Most important if there is a result.
        while($r = $result->fetch_assoc()) {
            if ($r['dbtable'] !== 'username') $r['user'] = cou($r['user']);
            if ($r['dbtable'] == 'block' || $r['dbtable'] == 'container') {
                if ($r['action'] == 3) {
                    $r['type'] = $entity[$r['type']];
                    $r['dbtable'] = 'kill';
                    }
                else {
                    if ($r['action'] == 2) $r['dbtable'] = 'click';
                    $r['type'] = [$r['type'],$material[$r['type']],$mcdataval[$r['type']]];
                }
            }
            if ($r['wid']) $r['wid'] = $world[$r['wid']];
            $return[] = $r;
        }

    }
}

$status['query'] = $sql;
$status['duration']= microtime(true) - $timer;

echo json_encode([$status,$return]);

// Raw around                       0.0085928440093994 seconds
// After adding username conversion 0.0088789463043213 seconds
// After adding block conversion    0.0090689659118652 seconds
?>