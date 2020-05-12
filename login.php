<?php
/**
 * Login JSON
 *
 * Returns login success as a JSON file
 * Redirects to index.php if login details are not defined in $_POST
 *
 * CoreProtect Lookup Web Interface
 * @author Simon Chuu
 * @copyright 2015-2020 Simon Chuu
 * @license MIT License
 * @link https://github.com/chuushi/CoreProtect-Lookup-Web-Interface
 * @since 1.0.0
 */

require_once 'res/php/Session.class.php';

$config = require_once 'config.php';

$session = new Session($config);
$logout = array_key_exists('logout', $_POST);

if (!(array_key_exists('username', $_POST) && array_key_exists('password', $_POST))
    && !$logout) {
    header("Location: index.php", true, 303);
    exit();
}

$out = [];
if ($logout) {
    $session->logout();
    $out["success"] = true;
} elseif ($session->login($_POST['username'], $_POST['password'], array_key_exists('remember', $_POST))) {
    $out["success"] = true;
    $out["username"] = $session->getUsername();
} else {
    $out["success"] = false;
}

header('Content-type:application/json;charset=utf-8');
echo json_encode($out);
