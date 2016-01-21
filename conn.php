<?php
// PHP code by SimonOrJ.  All Rights Reserved.
// Requires PHP 5.4+
/* GET outputs will be:
- array     action (a)
- array     user (u)
- int       radius (r)
- int       query limit (lim)
- string    time (t) in seconds
- array     block (b)
- string    chat/command/sign search (keyword)
- in_array  exclude (e) - ub in an array to add "NOT" in the injection string.
- array     center/first coordinate (xyz)
- array     second coordinate (xyz2)
- int       wordl (wid)
- bool      asending time? (asendt) (past-to-present or present-to-past)
- bool      session
- in_array  flag - su

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
    0 - Success
    1 - No Results
    2 - Early Termination
    3 - Database Not Found
    4 - CacheCtrl Value Not Found

*/

error_reporting(-1);
ini_set('display_errors', 'On');

// Record start time
$_timer = microtime(true);

// Code to run right before code terminates
function _shutdown() {
    global $out,$co_,$_timer,$searchSession;
    if(!isset($out[0]["status"])) {
        $out[0]["status"] = 2;
        $out[0]["reason"] = "Script error";
    }
    $out[0]["duration"] = microtime(true) - $_timer;
    echo json_encode($out);
}
register_shutdown_function("_shutdown");

// Modules
require "settings.php";
require "cachectrl.php";

// Get the requested stuffs: ($q)
if(!empty($_POST)) $q = $_POST;
elseif(!empty($_GET)) $q = $_GET;
else $q = [];

// Module Classes
$cc = new cachectrl($codb,$co_);

if(isset($q["SQL"])) {
    // Reserved for loading slightly more quickly
    $lookup = $codb->prepare($out[0]["SQL"] = $q["SQL"]);
}
else {
    foreach ($q as $key => $value) {
        if (in_array($key,["a","b","e","u","xyz","xyz2"],true)) $$key = explode(',', $value);
        elseif (in_array($key,["r","t","keyword","wid","rollback"],true)) $$key = $value;
        elseif (in_array($key,["unixtime","asendt"],true)) $$key = true;
    }
    
    
    // Defaults if the query or parts of the query is empty:
    if(!isset($a)) $a = ["block"];
    if(!isset($q["lim"])) $q["lim"] = 30;
    if(!isset($asendt)) $asendt = false;
    if(!isset($unixtime)) $unixtime = false;
    
    // coord xyz, xyz2, r
    if(isset($xyz) && (isset($r) || isset($xyz2))) {
        if(isset($r)) {
            $x = [$xyz[0]-$r,$xyz[0]+$r];
            $y = [$xyz[1]-$r,$xyz[1]+$r];
            $z = [$xyz[2]-$r,$xyz[2]+$r];
        }
        else {
            $x = [min($xyz[0],$xyz2[0]),max($xyz[0],$xyz2[0])];
            $y = [min($xyz[1],$xyz2[1]),max($xyz[1],$xyz2[1])];
            $z = [min($xyz[2],$xyz2[2]),max($xyz[2],$xyz2[2])];
        }
        if(isset($wid)) $coord = "wid=".$cc->getId($wid,"world")." AND ";
        else $coord = "";
        $coord .= "(x BETWEEN ".$x[0]." AND ".$x[1].") AND (y BETWEEN ".$y[0]." AND ".$y[1].") AND (z BETWEEN ".$z[0]." AND ".$z[1].")";
    }
    else $coord = false;
    
    // Time t, unixtime
    if(isset($t)) {
        $where[0] = "time";
        if($asendt) $where[0] .= ">=";
        else $where[0] .= "<=";
        if(!$unixtime) {
            $t = str_replace(",","",$t);
            $t = preg_split("/(?<=[wdhms])(?=\d)/",$t);
            $t2 = time();
            foreach($t as $value) {
                $value = preg_split("/(?<=\d)(?=[wdhms])/",$value,2);
                switch($value[1]) {
                    case "w":
                        $t2 -= $value[0]*604800;
                        break;
                    case "d":
                        $t2 -= $value[0]*86400;
                        break;
                    case "h":
                        $t2 -= $value[0]*3600;
                        break;
                    case "m":
                        $t2 -= $value[0]*60;
                        break;
                    case "s":
                        $t2 -= $value[0];
                }
            }
            $t = $t2;
        }
        $time .= $t;
    }
    else $time = "time<=".(time()+$timeOffset);
    
    // User u, e
    if(isset($u)) {
        $NOT = isset($e)&&in_array("u",$e,true) ? " NOT " : " ";
        foreach($u as $key => $us) $u[$key] = $cc->getId($us,"user");
        $userid = "user".$NOT."IN ('".implode("','",$u)."')";
        if(in_array("username",$a,true)) {
            foreach($u as $us) $us = $cc->getValue($us,"user");
            $username = "user".$NOT."IN ('".implode("','",$u)."')";
        }
    }
    else $userid = false;
    
    // block, kill, container rolled_back flag
    if(isset($rollback)) {
        $rbflag = "rolled_back=";
        $rbflag .= ($rollback) ? "1" : "0";
    }
    else $rbflag = false;

    // Block b, e
    $action = [[],false,false];
    if(in_array("block",$a,true) || in_array("click",$a,true)) {
        if(in_array("block",$a,true)) array_push($action[0],0,1);
        if(in_array("click",$a,true)) ($rbflag) ? $action[1] = true : $action[0][] = 2;
        if(isset($b)) {
            $NOT = isset($e)&&in_array("b",$e,true) ? " NOT " : " ";
            foreach($b as $key => $bk) $b[$key] = $cc->getId($bk,"material");
            $block = "type".$NOT."IN ('".implode("','",$b)."')";
        }
        else $block = false;
    }
    
    if(!empty($cc->error)) {
        $out[0]["status"] = 4;
        $out[0]["reason"] = "The following ID/value does not exist in the CoreProtect database.";
        $out[1] = $cc->error;
        exit();
    }
    
    // kill
    if(in_array("kill",$a,true)) {
        if(isset($b)) $action[2] = true;
        else $action[0][] = 3;
    }
    
    
    // Make query heading
    function sel($as,$cl) {
        if($as === 0) return ",NULL AS ".$cl;
        return ",".$as." AS ".$cl;
    }
    
    function sqlreq($table) {
        global $co_, $time, $username, $userid, $limit;
        $where[0] = $time;
        if($userid) $where[] = ($table == "username")?$username:$userid;
        switch($table) {
            case "block":
            case "session":
            case "container":
                global $coords;
                $ret = ",wid,x,y,z";
                if($coords) $where[] = $coords;
                if($table == "session") $ret .= sel(0,"type").sel(0,"data").sel(0,"amount").",action".sel(0,"rolled_back");
                else {
                    global $block, $rbflag;
                    $ret .= ",type,data";
                    if($table == ("block")) {
                        global $action;
                        $ret .= sel(0,"amount");
                        if($action[0]) $whereB[] = "action IN (".implode(",",$action[0]).")".(($block) ? " AND ".$block : "").(($rbflag) ? " AND ".$rbflag : "");
                        if($action[1]) $whereB[] = "action=2".(($block) ? " AND ".$block : "");
                        if($action[2]) $whereB[] = "action=3".(($rbflag) ? " AND ".$rbflag : "");
                        if(!empty($whereB)) $where[] = "(".implode(") OR (",$whereB).")";
                    }
                    else {
                        $ret .= ",amount";
                        if($block) $where[] = $block;
                        if($rbflag) $where[] = $rbflag;
                    }
                    $ret .= ",action,rolled_back";
                }
                break;
            case "chat":
            case "command":
            case "username_log":
                $ret = sel(0,"wid").sel(0,"x").sel(0,"y").sel(0,"z").sel(0,"type");
                $ret .= ($table == "username_log")? sel("uuid","data") : sel("message","data");
                $ret .= sel(0,"amount").sel(0,"action").sel(0,"rolled_back");
        }
        // "SELECT time,'".$table."' AS action,user".$ret." FROM ".$co_.$dt;
        
        return "SELECT time,'".$table."' AS 'table',user".$ret." FROM ".$co_.$table.((empty($where)) ? "" : " where ".implode(" AND ",$where));
    }
    
    foreach($a as $pa) {
        switch($pa) {
            case "block":
            case "click":
            case "kill":
                if(!isset($bflag)) {
                    $bflag = true;
                    $sql[] = sqlreq("block");
                }
                break;
            case "username":
                $sql[] = sqlreq("username_log");
                break;
            default:
                $sql[] = sqlreq($pa);
                break;
        }
    }
    
    $lookup = $codb->prepare($out[0]["SQL"] = ((count($sql) === 1) ? $sql[0] : implode(" UNION ",$sql))." ORDER BY time ".(($asendt)?"ASC":"DESC")." LIMIT ?,?;");
}
//var_dump($lookup);
//var_dump($codb->errorInfo());

if ($lookup->execute([(isset($q["offset"])?$q["offset"]:"0"),$q["lim"]])) {
    $out[0]["status"] = 0;
    $out[0]["reason"] = "Request successful";
//    $status["rows"] = $numrows;
    // Code Sanitaizer
    $json = [];
    while($r = $lookup->fetch(PDO::FETCH_ASSOC)) {
        if ($r["table"] !== "username_log") $r["user"] = $cc->getValue($r["user"],"user");
        if ($r["table"] == "block") {
            if ($r["action"] == 3) {
                $r["type"] = $cc->getValue($r["type"],"entity");
                $r["table"] = "kill";
                }
            else {
                if ($r["action"] == 2) $r["table"] = "click";
                $r["type"] = $cc->getValue($r["type"],"material");
            }
        }
        if ($r["wid"]) $r["wid"] = $cc->getValue($r["wid"],"world");
        $out[1][] = $r;
    }
    if(empty($out[1])) {
        $out[0]["status"] = 1;
        $out[0]["reason"] = "No results";
    }
}
?>