<?php
namespace BugOrderSystem;

require_once "vendor/autoload.php";
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

use PHPMailer\PHPMailer\PHPMailer;
use Log\ELogLevel;

class BugOrderSystem {

    private static $db;
    private static $email;
    /**
     * @var \Log
     */
    private static $log;
    /**
     * @var \ILogRead[]
     */
    public static $logReadHandlers = array();

    /**
     * @return \MysqliDb
     * @throws Exception
     */
    public static function GetDB() {
        if (isset(self::$db) && !empty(self::$db)) {
            return self::$db;
        }
        else {
            if (!class_exists("MysqliDb"))
                throw new Exception("Mandatory 'MysqliDB' class not exist!");

            $SqlCredential = \Credential::GetCredential('sql_' . Constant::MYSQL_SERVER . '_' . Constant::MYSQL_SERVER_PORT . '_' . Constant::MYSQL_DATABASE);
            self::$db = new \MysqliDb (Constant::MYSQL_SERVER, $SqlCredential->GetUsername(), $SqlCredential->GetPassword(), Constant::MYSQL_DATABASE, Constant::MYSQL_SERVER_PORT);
            return self::$db;
        }
    }

    /**
     * @return \Log
     * @throws Exception
     */
    public static function GetLog() {
        try {
            $username = "";
            if(session_status() !== PHP_SESSION_ACTIVE)
                @session_start();
            if (LoginC::ConnectedAs() !== false)
                $username = (string)$_SESSION[LoginC::ConnectedAs()];

            if (isset(self::$log) && !empty(self::$log)) {
                self::$log->SetUserName($username);
                return self::$log;
            }
            else {
                self::$log = new \Log(Constant::LOG_SYSTEM_NAME, $username, new \DateTimeZone(Constant::SYSTEM_TIMEZONE));
                $LogsBaseDir = \Services::GetBaseDir(Constant::SYSTEM_NAME) . Constant::LOG_SUBFOLDER;
                self::$logReadHandlers["file"] = self::$log->AddFileHandler(ELogLevel::DEBUG(), $LogsBaseDir, null, true,Constant::LOG_DEFAULT_MAX_FILE);
                self::$logReadHandlers["db"] = self::$log->AddMysqliDbHandler(ELogLevel::INFO(), self::GetDB(), "Monolog".Constant::SYSTEM_TEST_OR_EMPTY);

                foreach (Constant::WEBMASTER_EMAIL as $webmasterEmail)
                    self::$log->AddEmailHandler(ELogLevel::CRITICAL(), $webmasterEmail, Constant::SYSTEM_NAME);

                //$LogglyCredentials = \Credential::GetCredential('log_LOGGLY.xml');
                //\Services::dump($LogglyCredentials);
                //self::$log->AddLogglyHandler(ELogLevel::INFO(), $LogglyCredentials->GetPassword());

                return self::$log;
            }
        }
        catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $subject
     * @param string $message
     * @param bool $clear
     * @return PHPMailer
     * @throws Exception
     */
    public static function GetEmail(string $subject, string $message, bool $clear = True) {
        try {
            if (isset(self::$email) && !empty(self::$email)) {
                $ret = self::$email;
            }
            else {
                $Email = new PHPMailer(true);
                $Email->isSendmail();
                $Email->IsHTML(true);
                $Email->setFrom(Constant::EMAIL_SYSTEM_EMAIL, Constant::EMAIL_SYSTEM_NAME);
                $Email->ContentType = "text/html;charset=utf-8";
                $Email->headerLine("MIME-Version", 1.0);
                $Email->CharSet = 'utf-8';

                $body = <<<BUG
<html dir=rtl>
    <body>
       {$message}
    </body>
</html>
BUG;
                /*
                      <header>
        <style>

        </style>
    </header>
    <body style="background-color: #f2fcfe">
        <div style="background-color: #fcfcfc; top:10px; height: 60px; box-shadow: -18px 22px 20px -19px rgba(199,199,199,1);">
            <logo>
                <img src="https://bug.845.co.il/images/logo.png" style="float: left; position: absolute; vertical-align: middle;">
            </logo>
            <h2 style="text-align: center;">מערכת הזמנות B.O.S</h2>
        </div>
        <div>
            {$message}
        </div>
        <div style="text-align: center">
            <b>כל הזכויות שמורות - B.O.S</b>
        </div>
    </body>

                 */

                $Email->Subject = $subject;
                $Email->Body = $body;
                $Email->AltBody = strip_tags($message);

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
        catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }

    }
}