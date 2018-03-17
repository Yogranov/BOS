<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/3/2018
 * Time: 11:31 AM
 */

use GO\Scheduler;

require_once __DIR__ . '/Config.php';

try {
    // Create a new scheduler
    $scheduler = new Scheduler();

    //Emails
    $scheduler->php('Emails/DelayedOrder_Shop.php')->daily("08:00")->onlyOne();
    //$scheduler->php('Emails/NotifyClient.php')->onlyOne()->daily("09:00", "15:00");

    //Db Works

    // Let the scheduler execute jobs which are due.
    $scheduler->run();

} catch (Throwable $e) {
    Services::dump($e);
}
