<?php
/**
 * Lookup JSON
 *
 * Returns query results as a JSON file
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
 */

require_once 'res/php/StatementPreparer.class.php';
require_once 'res/php/PDOWrapper.class.php';
require_once 'res/php/Session.class.php';
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

$session = new Session($config);

// Allow for immediate login through request headers
if (array_key_exists('username', $_REQUEST) && array_key_exists('password', $_REQUEST)) {
    if (!$session->login($_REQUEST['username'], $_REQUEST['password'])) {
        http_response_code(401);
        $return[0]["status"] = 1;
        $return[0]["code"] = "Access Denied";
        $return[0]["reason"] = "The login credentials is incorrect.";
        exit();
    }
}

if (!$session->hasLookupAccess()) {
    http_response_code(401);
    $return[0]["status"] = 1;
    $return[0]["code"] = "Access Denied";
    $return[0]["reason"] = "You must log in first to access the data.";
    exit();
}

$serverName = $_REQUEST['server'];
if ($serverName == null) {
    $return[0]["status"] = 1;
    $return[0]["code"] = "Request Error";
    $return[0]["reason"] = "No server specified.";
    exit();
}

$server = $config['database'][$serverName];
if (!isset($server)) {
    $return[0]["status"] = 1;
    $return[0]["code"] = "Configuration Error";
    $return[0]["reason"] = "The specified server '$serverName' is not configured.";
    exit();
}

$prep = new StatementPreparer($server['prefix'], $_REQUEST, $config['form']['count'], $config['form']['moreCount']);
$wrapper = new PDOWrapper($server);
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
    if (!isset($_REQUEST['offset']) && $server['mapLink']) $return[0]["mapHref"] = $server['mapLink'];

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
