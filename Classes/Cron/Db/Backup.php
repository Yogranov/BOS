<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 4/6/2018
 * Time: 7:09 PM
 */

use CronJob\Constants;

$PageName = "/Db/Backup.php";
require_once __DIR__ . '/../Config.php';
require_once \BugOrderSystem\Constant::DB_CLASS_DIR.'backup_db.php';

if (!Constants::EMAIL_SEND && !Constants::DEBUG) {
    die();
}

try {
    $logText = $logPrePendText . "החל ריצה אוטומטית";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array(), false, false);

    $backup_manager = new Db_Tool_Backup;
    $backup_manager->backup();
    $backup_manager->cleanup();

    $logText = $logPrePendText . "הסתיימה הריצה - גובו {SystemBackups} בסיסי הנתונים במערכת";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array("SystemBackups" => count(\BugOrderSystem\Constant::DB_BACKUP_DBS), "DbBackupsName" => \BugOrderSystem\Constant::DB_BACKUP_DBS),false, false);

} catch(Throwable $e) {
    $logText = $logPrePendText . "הריצה לא הצליחה - אירעה שגיאה כללית {ErrorMessage}";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("ErrorMessage" => $e->getMessage(), "ErrorObject" => $e),false, false);
}
