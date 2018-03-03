<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/3/2018
 * Time: 11:31 AM
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/../BugOrderSystem.php';

use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

//$scheduler->php('Emails.php')->onlyOne()->daily("09:30");
//$scheduler->php('Emails.php')->onlyOne()->daily("19:00");

// Let the scheduler execute jobs which are due.
$scheduler->run();
