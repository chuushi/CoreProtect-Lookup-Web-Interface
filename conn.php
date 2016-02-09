<?php
/* CoreProtect LWI v0.7.0-beta. February 7, 2016
 * PHP code by SimonOrJ.
 * Requires PHP 5.4+
 * Database tables to use:
block (a: block, click, kill)
entity (for mob kills) (use with block table)
sign (with block>data minecraft:sign and minecraft:wall_sign)
skull (with block>data minecraft:skull)
container (a: container) (maybe use with container blocks)
chat (a: chat)
command (a: command)
session (a:session)
username_log (a:username)

Output status codes:
    0 - Success
    1 - No Results
    2 - SQL Query Unsuccessful
    3 - Database Connection Failed
    4 - Values from cachectrl Not Found
    6 - No status code
    7 - (JS-side) Invalid response
*/
// Testing script
//error_reporting(-1);ini_set('display_errors', 'On');

// Record start time
$_timer = microtime(true);

// Code to run right before code terminates
function _shutdown() {
    global $out,$co_,$_timer,$searchSession;
    if(!isset($out[0]["status"])) {
        $out[0]["status"] = 6;
        $out[0]["reason"] = "Uncaught error has made the script terminate too early.";
    }
    $out[0]["duration"] = microtime(true) - $_timer;
    echo json_encode($out);
}
register_shutdown_function("_shutdown");

// Modules
/*if(file_exists("cache/setup.php")) require "cache/config.php";
else {
    $out[0]["status"] = 3;
    $out[0]["reason"] = "Settings not configured; please visit config.php first.";
    exit();
}*/
require "PDO.php";
require "cachectrl.php";
require "co2mc.php";

// Get the requested stuffs: ($q)
$q = $_REQUEST;

// Module Classes
$cc = new cachectrl($codb,$co_,$legacySupport);
$cm = ($translateCo2Mc) ? new co2mc() : new keepCo();

// Special Material list
$signBlocks = ["minecraft:standing_sign","minecraft:wall_sign"];

// Compiling code
if(isset($q["SQL"])) {
    // Reserved for loading slightly more quickly
    $lookup = $codb->prepare($out[0]["SQL"] = $q["SQL"]);
    $out[0]["SQLqs"] = $q["SQLqs"];
    
    // Defaults if the query or parts of the query is empty:
    if(empty($q["lim"])) $q["lim"] = 10;
}
else {
    foreach ($q as $key => $value) {
        if (in_array($key,["a","b","e","u","xyz","keyword"],true)) {if((is_array($value)&&!in_array("",$value,true))||(is_string($value)&&($value!==""))) $$key = (is_array($value))?$value:($key=="keyword"?str_getcsv($value):explode(',',str_replace(' ', '', $value)));}
        elseif (in_array($key,["r","t","wid","rollback"],true)) {if($value!=="") $$key = $value;}
        elseif (in_array($key,["unixtime","asendt"],true)) {if($value!=="") $$key = true;}
        elseif ($key == "xyz2") {
            if((is_array($value)&&!in_array("",$value,true))||(is_string($value)&&($value!=="")))$$key = (is_array($value))?$value:explode(',', $value);
            elseif(isset($value[0])&&$value[0]!=="") $r = $value[0];
        }
    }
    if(isset($xyz2) && !isset($xyz2[2])) $r = $xyz2[0];
    
    // Defaults if the query or parts of the query is empty:
    if(empty($a)) $a = ["block"];
    if(empty($q["lim"])) $q["lim"] = 30;
    if(!isset($asendt)) $asendt = false;
    if(!isset($unixtime)) $unixtime = false;
    
    // coord xyz, xyz2, r
    if((isset($xyz) && (isset($r) || isset($xyz2)))||isset($wid)) {
        if(isset($xyz) && isset($r)) {
            $x = [$xyz[0]-$r,$xyz[0]+$r];
            $y = [$xyz[1]-$r,$xyz[1]+$r];
            $z = [$xyz[2]-$r,$xyz[2]+$r];
            $coord = "(x BETWEEN ".$x[0]." AND ".$x[1].") AND (y BETWEEN ".$y[0]." AND ".$y[1].") AND (z BETWEEN ".$z[0]." AND ".$z[1].")";
        }
        elseif(isset($xyz) && isset($xyz2)) {
            $x = [min($xyz[0],$xyz2[0]),max($xyz[0],$xyz2[0])];
            $y = [min($xyz[1],$xyz2[1]),max($xyz[1],$xyz2[1])];
            $z = [min($xyz[2],$xyz2[2]),max($xyz[2],$xyz2[2])];
            $coord = "(x BETWEEN ".$x[0]." AND ".$x[1].") AND (y BETWEEN ".$y[0]." AND ".$y[1].") AND (z BETWEEN ".$z[0]." AND ".$z[1].")";
        }
        if(isset($wid)) {
            if(isset($coord)) $coord .= " AND ";
            else $coord = "";
            $coord .= "wid=".$cc->getId($wid,"world");
        }
    }
    else $coord = false;
    
    // Time t, unixtime
    if(isset($t)) {
        $time = "time";
        if($asendt) $time .= ">=";
        else $time .= "<=";
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
    if(in_array("block",$a,true) || in_array("click",$a,true) || in_array("container",$a,true)) {
        if(in_array("block",$a,true)) array_push($action[0],0,1);
        if(in_array("click",$a,true)) ($rbflag) ? $action[1] = true : $action[0][] = 2;
        if(isset($b)) {
            $NOT = isset($e)&&in_array("b",$e,true) ? " NOT " : " ";
            foreach($b as $key => $bk) {
                $bk = $cm->getCo($bk);
                $b[$key] = $bk;
                if($legacySupport) if($bk !== ($bk2=preg_replace("/^minecraft:/","",$bk))) $b[] = $bk2;
            }
            foreach($b as $key => $bk) $b[$key] = $cc->getId($bk,"material");
            $block = "type".$NOT."IN ('".implode("','",$b)."')";
        }
        else $block = false;
    }
    
    if(!empty($cc->error)) {
        $out[0]["status"] = 4;
        $out[0]["reason"] = "Username/Block Input not found";
        $out[1] = $cc->error;
        exit();
    }
    
    // kill
    if(in_array("kill",$a,true)) {
        if(isset($b)) $action[2] = true;
        else $action[0][] = 3;
    }
    
    // keyword
    if(isset($keyword)) {
        foreach($keyword as $word) {
            $terms = [];
            $words = str_getcsv($word,' ');
            foreach($words as $val) $terms[] = "message LIKE '%".$val."%'";
            $search[] = "(".implode(" AND ",$terms).")";
        }
        $search = "(".implode(" OR ",$search).")";
        /*if(in_array("block",$a,true)) {
            foreach($keywords as $val) $serachSign[] = "(line_1 LIKE '%".$val."%' OR line_2 LIKE '%".$val."%' OR line_3 LIKE '%".$val."%' OR line_4 LIKE '%".$val."%')";
            $searchSign = "(".implode(" AND ",$searchSign).")";
        }*/
    }
    else $search = false;
    
    // Function for heading
    function sel($as,$cl) {
        if($as === 0) return ",NULL AS ".$cl;
        return ",".$as." AS ".$cl;
    }

    // Make query heading
    function sqlreq($table) {
        global $co_, $time, $username, $userid, $limit, $search;
        $where[0] = $time;
        if($userid) $where[] = ($table == "username")?$username:$userid;
        switch($table) {
            case "block":
            case "session":
            case "container":
                global $coord;
                $ret = ",wid,x,y,z";
                if($coord) $where[] = $coord;
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
                if($search) $where[] = $search;
            case "username_log":
                $ret = sel(0,"wid").sel(0,"x").sel(0,"y").sel(0,"z").sel(0,"type");
                $ret .= ($table == "username_log")? sel("uuid","data") : sel("message","data");
                $ret .= sel(0,"amount").sel(0,"action").sel(0,"rolled_back");
        }
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
    // SELECT time,'block' AS 'table',user,wid,x,y,z,type,data,NULL AS amount,action,rolled_back FROM co_block where type LIKE ($signBlocks) AND x= AND y= AND z= AND time<=1454881758 ORDER BY time DESC LIMIT 1;
    // SELECT time,user,x,y,z,wid from co_sign where (line like '%stuff%') ORDER BY time DESC LIMIT 1;
    $tables = "";
    if(($out[0]["SQLqs"]=count($sql)) > 1) foreach($sql as $key => $value) {
        if($key) $tables .= " UNION ALL ";
        $tables .= "SELECT * FROM (".$value." ORDER BY time ".(($asendt)?"ASC":"DESC")." LIMIT ?) AS T".$key;
    }
    $lookup = $codb->prepare($out[0]["SQL"] = (($out[0]["SQLqs"] > 1)?$tables:$sql[0])." ORDER BY time ".(($asendt)?"ASC":"DESC")." LIMIT ?,?;");
}

if($out[0]["SQLqs"] > 1) for($i = 1; $i <= $out[0]["SQLqs"]; $i++) {
    $lookup->bindValue($i,(isset($q["offset"])?intval($q["offset"]):0)+intval($q["lim"]),PDO::PARAM_INT);
}
else $out[0]["SQLqs"] = 0;

$lookup->bindValue($out[0]["SQLqs"]+1,(isset($q["offset"])?intval($q["offset"]):0),PDO::PARAM_INT);
$lookup->bindValue($out[0]["SQLqs"]+2,intval($q["lim"]),PDO::PARAM_INT);

if ($lookup->execute()) {
    $out[0]["status"] = 0;
    $out[0]["reason"] = "Request successful";
//    $status["rows"] = $numrows;
    // Code Sanitaizer
    while($r = $lookup->fetch(PDO::FETCH_ASSOC)) {
        if ($r["table"] !== "username_log") $r["user"] = $cc->getValue($r["user"],"user");
        if ($r["table"] == "block" || $r["table"] == "container") {
            if ($r["action"] == 3) {
                $r["type"] = $cc->getValue($r["type"],"entity");
                $r["table"] = "kill";
                }
            else {
                if ($r["action"] == 2) $r["table"] = "click";
                $r["type"] = $cm->getMc($cc->getValue($r["type"],"material"));
                if(in_array($r["type"],$signBlocks,true)) {
                    $sign = $codb->query("SELECT line_1,line_2,line_3,line_4 FROM ".$co_."sign WHERE x=".$r["x"]." AND y=".$r["y"]." AND z=".$r["z"]." AND time".(($r["action"]=="0")?"<":">=").$r["time"]." ORDER BY time ".(($r["action"]=="0")?"DESC":"ASC")." LIMIT 1;")->fetch(PDO::FETCH_NUM);
                    if($sign!=NULL)foreach($sign as $val) {
                        if(trim($val)!="") {
                            $r["signdata"]=$sign;
                            break;
                        }
                    }
                }
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
else {
    $out[0]["status"] = 2;
    $out[0]["reason"] = "SQL Execution Unsuccessful.";
    $out[1] = $lookup->errorInfo();
}
?>