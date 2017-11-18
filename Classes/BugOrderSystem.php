<?php
namespace BugOrderSystem;

require "MysqliDb.php";
require "PHPMailer/PHPMailer.php";
require "Log/Log.php";

//aux classes
include_once "Services.php";
include_once "Exception.php";
include_once "Constant.php";
include_once "Cookie.php";

//Enums
//include_once "Enum.php";
include_once "ESellerStatus.php";
include_once "EOrderStatus.php";
include_once "EProductStatus.php";

//inner classes
include_once "Region.php";
include_once "Shop.php";
include_once "Client.php";
include_once "LoginC.php";

use Log\ELogLevel;

class BugOrderSystem {
    
    private static $db;
    private static $email;
    private static $log;

    
   // const EMAIL_USERNAME;

    /**
     * @return \MysqliDb|string
     * @throws \Exception
     */
    public static function GetDB() {
        if (isset(self::$db) && !empty(self::$db)) {
            return self::$db;
        }
        else {
            if (!class_exists("MysqliDb"))
                throw new \Exception("Mandatory 'MysqliDB' class not exist!");
            
            self::$db = new \MysqliDb (Constant::MYSQL_SERVER, Constant::MYSQL_DATABASE_USERNAME, Constant::MYSQL_DATABASE_PASSWORD, Constant::MYSQL_DATABASE, Constant::MYSQL_SERVER_PORT);
            return self::$db;
        }
    }

    /**
     * @return \Log
     */
    public static function GetLog() {
        if (isset(self::$log) && !empty(self::$log))
            return self::$log;
        else {
            self::$log = new \Log(Constant::SYSTEM_LOG_NAME);
            $LogsBaseDir = \Services::GetBaseDir(Constant::SYSTEM_NAME) . Constant::LOG_SUBFOLDER;
            self::$log->AddFileHandler(ELogLevel::DEBUG(), $LogsBaseDir, null, Constant::DEFAULT_MAX_FILE);
            self::$log->AddEmailHandler(ELogLevel::CRITICAL(), Constant::WEBMASTER_EMAIL, Constant::SYSTEM_NAME);
            return self::$log;
        }
    }

    /**
     * @param string $subject
     * @param string $message
     * @param bool $clear
     * @return \PHPMailer|string
     */
    public static function GetEmail(string $subject, string $message, bool $clear = True) {
        if (isset(self::$email) && !empty(self::$email)) {
            $ret = self::$email;
        }
        else {
            $Email = new \PHPMailer();
            $Email->isSendmail();
            $Email->IsHTML(true);
            $Email->setFrom('OrderSystem@bug.co.il', 'Bug_Order_System(BoS) No-Reply');
            $Email->CharSet = 'utf-8';

            $body = <<<BUG
<html dir=rtl>
    <body>
        {$message}
    </body>
</html>
BUG;
            $Email->Subject = $subject;
            $Email->Body = $body;


            $ret = self::$email = $Email;
        }

        if ($clear)
        {
            self::$email->ClearAddresses(); //
            self::$email->ClearCCs();
            self::$email->ClearBCCs();
            self::$email->clearAttachments();
        }
        return $ret;
    }


}

?>