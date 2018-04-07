<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/3/2018
 * Time: 11:31 AM
 */

use GO\Scheduler;
use GO\Job;

require_once __DIR__ . '/Config.php';

try {
    // Create a new scheduler
    $scheduler = new Scheduler();
    //$scheduler->clearJobs();

    //Emails
    $scheduler->php('Emails/DelayedOrder_Shop.php')->inForeground()->at("0 8 * * 0-5"); // https://crontab.guru/#0_8_*_*_0-5
    $scheduler->php('Emails/DelayedOrder_Shop.php')->inForeground()->at("0 18 * * 6"); // https://crontab.guru/#0_18_*_*_6

    $scheduler->php('Emails/OrderUpdateNotify_Client.php')->inForeground()->at("0 9,12,16 * * 0-5"); // https://crontab.guru/#0_9,12,16_*_*_0-5

    $scheduler->php('Emails/DbBackup_Webmaster.php')->inForeground()->at("0 8 * * 0"); // https://crontab.guru/#0_8_*_*_0

    //db backups
    $scheduler->php('Db/Backup.php')->inForeground()->at("0 0 * * *"); // https://crontab.guru/#0_0_*_*_*

    // Let the scheduler execute jobs which are due.
    $scheduler->run();

    /** @var Job $ExecutedJob */
    foreach ($scheduler->getExecutedJobs() as $ExecutedJob) {

    }

    /** @var Job $FailedJob */
    foreach ($scheduler->getFailedJobs() as $FailedJob) {

    }

} catch (Throwable $e) {
    Services::dump($e);
}
