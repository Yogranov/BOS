<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 14-Oct-17
 * Time: 17:53
 */

namespace BugOrderSystem;

class Constant
{
    const SYSTEM_DEBUG = true;
    /*
     * Oper: Operational Website
     * Test: Development Website for testing
     */
    const SYSTEM_TEST_OR_OPER = "Test";
    const SYSTEM_VERSION = "2.3";
    const SYSTEM_NAME = "BugOrderSystem".self::SYSTEM_TEST_OR_OPER ;
    const SYSTEM_TIMEZONE = "Asia/Jerusalem";
    const SYSTEM_DOMAIN = "https://yourdomain.com/";
    const SYSTEM_LOCAL_ABSOLUTE_PATH = "/path/to/dir";
    const SYSTEM_WEBHOST_ROOT_DIRECTORY = "/public_html/";
    const SYSTEM_MAIN_SUBFOLDER = "Bug/";
    const SYSTEM_SUBFOLDER = self::SYSTEM_MAIN_SUBFOLDER . self::SYSTEM_TEST_OR_OPER ."/";

    const API_URL = self::SYSTEM_DOMAIN.self::SYSTEM_SUBFOLDER."API_CALLS.php";
    const WEBMASTER_EMAIL = array("example1@example.com", "example2@example.com");

    //Products
    const PRODUCT_MAX_QUANTITY = 100;
    const PRODUCTS_BARCODE_ONLY_INT = false;
    const PRODUCTS_STATUS_STYLE = array(
        1 => array("",""),
        2 => array("",""),
        3 => array("color: rgb(0,140,0)",""),
        4 => array("color: rgb(0,173,204)",""),
        5 => array("color: rgb(150,0,0)","table-success"),
        6 => array("color: rgb(120,90,200)",""),
        7 => array("color: #5EC14C; text-decoration: line-through",""),
        8 => array("color: rgb(255,0,0); text-decoration: line-through;",""),
        10 => array("color: #e68a00;",""),
        "default" => array("", "")
    );
    const PRODUCTS_STATUS_NOT_SELECTABLE = array(
        "Message_Sent"
    );

    //Orders && Products
    const ORDER_ALERT_DAYS_REGION = 7;
    const ORDER_STATUS_STYLE = array(
        1 => array("", ""),
        2 => array("color: rgb(0,140,0)",""),
        3 => array("color: rgb(0,173,204)",""),
        4 => array("color: rgb(150,0,0)","w3-animate-fading"),
        5 => array("color: rgb(150,130,50)",""),
        6 => array("color: rgb(120,90,200)",""),
        7 => array("",""),
        8 => array("",""),
        "default" => array("", "")
    );
    const ORDER_PRODUCT_STATUS_TO_ORDER_STATUS_MAP = array(
        "Created" => "Open",
        "Pre_order" => "Pre_order",
        "Ordered" => "Ordered",
        "Enroute" => "Enroute",
        "Arrived" => "Arrived",
        "Message_Sent" => "Arrived",
        "Client_Informed" => "Client_Informed",
        "Delivered" => "Delivered",
        "Aborted" => "Aborted"
    );

    //Log
    const LOG_SYSTEM_NAME = self::SYSTEM_NAME."_LOG";
    const LOG_SUBFOLDER = Constant::SYSTEM_LOCAL_ABSOLUTE_PATH . Constant::SYSTEM_WEBHOST_ROOT_DIRECTORY . Constant::SYSTEM_MAIN_SUBFOLDER . "Logs/";
    const LOG_DEFAULT_MAX_FILE = 12;
    const LOG_MESSAGE_TYPE_PROPERTY_MAP = array(
        "default" => "",
        "ERROR" => "table-danger"
    );
    /**
     * @param string $messageType
     * @return string
     */
    public static function GetMessageRowClass(string $messageType) {
        if (array_key_exists($messageType, self::LOG_MESSAGE_TYPE_PROPERTY_MAP))
            $outputProperty = "class = ".self::LOG_MESSAGE_TYPE_PROPERTY_MAP[$messageType];
        else
            $outputProperty = self::LOG_MESSAGE_TYPE_PROPERTY_MAP['default'];

        return $outputProperty;
    }

    //Mysql
    const MYSQL_SERVER = "";
    const MYSQL_PROTOCOL = "tcp";
    const MYSQL_SERVER_PORT = ;
    const MYSQL_DATABASE = "";

    //DB Backup
    const DB_CLASS_DIR = self::SYSTEM_LOCAL_ABSOLUTE_PATH.self::SYSTEM_WEBHOST_ROOT_DIRECTORY.'/'.self::SYSTEM_SUBFOLDER."DbBackups/";
    const DB_BACKUP_DIR = self::SYSTEM_LOCAL_ABSOLUTE_PATH.'/DB-backups/';
    const DB_BACKUP_FILES = 14;
    const DB_BACKUP_COMPRESSION = ""; // gzip, bzip2, etc
    const DB_BACKUP_DBS = array(
        self::MYSQL_DATABASE
    );

    //Email Template's
    const EMAIL_SYSTEM_EMAIL = "system@email.com";
    const EMAIL_SYSTEM_NAME = "Bug_Order_System(BoS) No-Reply";

    //TODO: Need to change text
    const EMAIL_CLIENT_PRODUCT_ARRIVED = <<<EMAIL
    שלום <span style="">{Name}</span>,<br/>
    <br/>
    <h2>אנו שמחים להודיעך כי הפריטים הבאים מהזמנה {OrderNumber} הגיעו לסניף {ShopName} וממתינים לאיסוף:</h2>
    <table style="padding-top: 12px;
                        padding-bottom: 12px;
                        text-align: center;
                        border: 1px solid #ddd;
                        border-collapse: collapse;
                        width: 95%;
                        direction: rtl;
                        ">
          <thead>
            <tr style="font-size: 20px; background-color: #af574d; color: white;">
              <th scope="col"></th>
              <th scope="col">פריט</th>
              <th scope="col">כמות</th>
            </tr>
          </thead>
          <tbody>
            {ClientOrdersList}
          </tbody>
        </table>
        <br>
        בברכה,<Br/>
        <br/>
        סניף {ShopName}

        <img src='{serverLoc}Mailchecker.php/?orderId={OrderNumber}'>
EMAIL;
    const EMAIL_CLIENT_PRODUCT_ARRIVED_TABLE = <<<EMAIL
    <tr>
      <th scope="row">{Number}</th>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{ProductName}</td>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{Quantity}</td>
    </tr>
EMAIL;

    const EMAIL_SHOP_ORDERS_NEED_ATTENTION = <<<EMAIL
    שלום <span style="">{ShopName}</span>,<br/>
    <br/>
    <h2>קיימות מספר הזמנות אשר מצריכות תשומת לב:</h2>
    <table style="padding-top: 12px;
                        padding-bottom: 12px;
                        text-align: center;
                        border: 1px solid #ddd;
                        border-collapse: collapse;
                        width: 95%;
                        direction: rtl;
                        ">
          <thead>
            <tr style="font-size: 20px; background-color: #af574d; color: white;">
              <th scope="col"></th>
              <th scope="col">מספר הזמנה</th>
              <th scope="col">זמן עדכון אחרון</th>
              <th scope="col">מוכרן</th>
            </tr>
          </thead>
          <tbody>
            {ShopOrdersList}
          </tbody>
        </table>
        <br><br>
   נא לדאוג לטפלם בהקדם! 
EMAIL;
    const EMAIL_SHOP_ORDERS_NEED_ATTENTION_TABLE = <<<EMAIL
    <tr>
      <th scope="row">{number}</th>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{orderId}</td>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{lastUpdateTime}</td>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{sellerName}</td>
    </tr>
EMAIL;

    const EMAIL_SELLER_NEED_TO_ORDER = <<<EMAIL
    שלום <span style="">{Name}</span>,<br/>
    <br/>
   הזמנה מספר <span style="font-weight: bold;">{OrderID}</span> על שם <span style="">{ClientName}</span>,<Br/>
  אשר נפתחה אתמול, עדיין לא הוזמנה.<br/>
   <br/>
   נא לדאוג להזמינה בהקדם!
EMAIL;

    const EMAIL_SHOP_NEED_TO_ORDER = <<<EMAIL
    שלום <span style="">{Name}</span>,<br/>
    <br/>
   הזמנה מספר <span style="font-weight: bold;">{OrderId}</span> על שם <span style="">{ClientName}</span>,<Br/>
  אשר נפתחה אתמול על ידי {SellerName}, עדיין לא הוזמנה.<br/>
   <br/>
   נא לדאוג להזמינה בהקדם!
EMAIL;

    const EMAIL_CLIENT_ORDER_ARRIVED = <<<EMAIL
    שלום {Name},<br/>
    <br/>
    ההזמנה שביצעת בסניף {ShopName} הגיעה לסניף ומוכנה לאיסוף.<br/>
    <br/>
    בברכה,<Br/>
    <br/>
    סניף {ShopName}
EMAIL;

    const EMAIL_CLIENT_SUMMERY_ORDER = <<<EMAIL
<div style="direction: rtl; font-family: arial, sans-serif; background: linear-gradient(to right, #f2fcfe, #f2fcfe); /*Standard*/">
    <div style="padding: 20px">
        <h1 style="font-style: italic">שלום {ClientName}, </h1>
        <h2 style="color: #555555"> אנו שמחים כי בחרת להזמין מאיתנו, אנו נעשה את מירב המאמצים בכדי לספק לך את המוצר בזמן הקצר ביותר.</h2>
        <h2 style="padding: 20px 20px 0 0 ;">צרפנו עבורך סיכום ודף מעקב אחר ההזמנה ({OrderId}):</h2>
        <table style="padding-top: 12px;
                        padding-bottom: 12px;
                        text-align: center;
                        border: 1px solid #ddd;
                        border-collapse: collapse;
                        width: 95%;
                        direction: rtl;
                        ">
          <thead>
            <tr style="font-size: 20px; background-color: #af574d; color: white;">
              <th scope="col"></th>
              <th scope="col">מוצר</th>
              <th scope="col">כמות</th>
            </tr>
          </thead>
          <tbody>
            {OrderProductSummary}
          </tbody>
        </table>
        <br><br>
    
        <a style="text-decoration: none; font-size: 24px; padding-right: 20px" href="https://yourdomain.com/statuscheck.php/?id={StatusCheckURL}">לחץ כאן לצפייה בהזמנה.</a>
    
        <div style="font-size: 18px; padding: 0 20px 20px 0;">
        <br>
        תודה רבה, <br>
        סניף {ShopName}</div>
    </div>
</div>
EMAIL;

    const EMAIL_CLIENT_SUMMERY_ORDER_TABLE = <<<EMAIL
    <tr>
      <th scope="row">{number}</th>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{productName}</td>
      <td style="border: 1px solid #ddd; padding: 8px; font-size: 16px;">{productQuantity}</td>
    </tr>
EMAIL;


}