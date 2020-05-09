<?php
/**
 * Lookup PHP application
 * @author Simon Chuu
 */

require_once 'res/php/StatementPreparer.class.php';
require_once 'res/php/PDOWrapper.Class.php';
$config = require_once 'config.php';

$return = [["time" => microtime(true)]];

register_shutdown_function(function () {
    global $return;

    // Set type to application/json
    header('Content-type:application/json;charset=utf-8');

    if(!isset($return[0]["status"]))
        $return[0]["status"] = -1;
    $return[0]["duration"] = microtime(true) - $return[0]["time"];
    echo json_encode($return);
});

// Get the input parameters
$request = $_REQUEST;

// TODO: get prefix from config
$prep = new StatementPreparer("co_", $request, $config['form']['count'], $config['form']['moreCount']);

// TODO: get database info from config
$wrapper = new PDOWrapper(["type" => "sqlite", "path" => "./database.db"]);
$pdo = $wrapper->initPDO();

if (!$pdo) {
    $return[0]["status"] = 1;
    $return[0]["code"] = $wrapper->error()[0];
    $return[0]["reason"] = $wrapper->error()[1];
    exit();
}

$lookup = $pdo->prepare($prep->preparedStatementData());

if (!$lookup) {
    $return[0]["status"] = 2;
    $return[0]["code"] = $pdo->errorInfo()[0];
    $return[0]["driverCode"] = $pdo->errorInfo()[1];
    $return[0]["reason"] = $pdo->errorInfo()[2];
    exit();
}

if ($lookup->execute($prep->preparedParams())) {
    $return[0]["status"] = 0;
    $return[1] = [];
    while($r = $lookup->fetch(PDO::FETCH_ASSOC)) {
        // Treat numbers as integers
        $r["rowid"] = intval($r["rowid"]);
        $r["time"] = intval($r["time"]);
        if ($r["action"] !== null) {
            $r["action"] = intval($r["action"]);
        }
        if ($r["world"] !== null) {
            $r["x"] = intval($r["x"]);
            $r["y"] = intval($r["y"]);
            $r["z"] = intval($r["z"]);
        }
        if ($r["amount"] !== null) {
            $r["amount"] = intval($r["amount"]);
        }
        if ($r["rolled_back"] !== null) {
            $r["rolled_back"] = intval($r["rolled_back"]);
        }
        $return[1][] = $r;
    }
} else {
    $return[0]["status"] = 2;
    $return[0]["code"] = $lookup->errorInfo()[0];
    $return[0]["driverCode"] = $lookup->errorInfo()[1];
    $return[0]["reason"] = $lookup->errorInfo()[2];
    exit();
}

