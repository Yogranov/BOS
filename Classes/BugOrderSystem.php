<?php
namespace BugOrderSystem;

require "MysqliDb.php";
require "PHPMailer/PHPMailer.php";

//aux classes
include_once "Services.php";
include_once "Exception.php";
include_once "Constant.php";
include_once "Cookie.php";

//Enums
include "Enum.php";
include_once "ESellerStatus.php";
include_once "EOrderStatus.php";
include_once "EProductStatus.php";

//inner classes
include_once "Region.php";
include_once "Shop.php";
include_once "Client.php";
include_once "LoginC.php";




class BugOrderSystem {
    
    private static $db = "";
    private static $email = "";
    
   // const EMAIL_USERNAME;
    
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