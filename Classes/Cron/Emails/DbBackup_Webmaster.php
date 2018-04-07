<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 4/5/2018
 * Time: 1:13 PM
 */

use CronJob\Constants;

$PageName = "/Email/DelayedOrder_Shop.php";
require_once __DIR__ . '/../Config.php';

if (!Constants::EMAIL_SEND && !Constants::DEBUG) {
    die();
}

try {
    $logText = $logPrePendText . "החל ריצה אוטומטית";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array(), false, false);

    /** @var SplFileObject[] $lastBackups */
    $lastBackups = array();
    foreach (\BugOrderSystem\Constant::DB_BACKUP_DBS as $dbToBackup) {
        $dir = \BugOrderSystem\Constant::DB_BACKUP_DIR . $dbToBackup.'/';
        foreach (scandir($dir, SCANDIR_SORT_DESCENDING) as $file) {
            $object = new SplFileObject($dir.$file);
            if ($object->isFile()) {
                $lastBackups[$dbToBackup] = $object;
                break;
            }
        }
    }

    $message = "מצורף במייל גיבוי לכלל בסיסי הנתונים של המערכת כפי שהוגדרו";
    $EmailObject = \BugOrderSystem\BugOrderSystem::GetEmail("BOS - גיבוי לבסיסי הנתונים", $message);

    foreach (\BugOrderSystem\Constant::WEBMASTER_EMAIL as $webmasterEmail)
        $EmailObject->addAddress($webmasterEmail);

    foreach ($lastBackups as $dbToBackup => $fileObject) {
        $EmailObject->addAttachment($fileObject->getRealPath(), $fileObject->getFilename(), "base64", $fileObject->getType());
    }

    if (!$EmailObject->send())
        throw new Exception($EmailObject->ErrorInfo);

    $logText = $logPrePendText . "הסתיימה הריצה - נשלח אימייל עם בסיסי הנתונים '{DbToBackup}' לכתובות הבאות: '{EmailTo}'";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array("DbToBackup" => Services::ArrayToMulti(array_keys($lastBackups), ","), "EmailTo" => $EmailObject->getToAddresses(), "Email" => $EmailObject),false, false);

} catch(Throwable $e) {
    $logText = $logPrePendText . "הריצה לא הצליחה - אירעה שגיאה כללית {ErrorMessage}";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("ErrorMessage" => $e->getMessage(), "ErrorObject" => $e),false, false);
}