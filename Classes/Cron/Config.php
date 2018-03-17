<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/17/2018
 * Time: 11:03 AM
 */

use CronJob\Constants;

require_once __DIR__.'/Constants.php';
require_once __DIR__.'/vendor/autoload.php';

require_once __DIR__.'/../BugOrderSystem.php'; //External class

if (Constants::DEBUG || \BugOrderSystem\Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
else {
    if (array_key_exists("REMOTE_ADDR", $_SERVER) && (empty($_SERVER["REMOTE_ADDR"]) || !in_array($_SERVER["REMOTE_ADDR"], \CronJob\Constants::ALLOWED_IPS))) {
        header("HTTP/1.1 403 Forbidden");
        die();
    }
}

$logPrePendText =  Constants::LOG_TEXT_PREPEND . " - " . $PageName . " - ";