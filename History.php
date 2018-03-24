<?php
/**
 * Created by PhpStorm.
 * User: shalev
 * Date: 3/10/2018
 * Time: 11:25 AM
 */

namespace BugOrderSystem;

use Log\Message;

session_start();
require_once "Classes/BugOrderSystem.php";

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if (isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

$shopObj = &Shop::GetById($shopId);

//setting header
require_once "Header.php";
//setting page title
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageTitle", "היסטוריית מערכת");

//setting menu bar
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "shopName", $shopObj->GetShopName());
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "ordersBoardClass", "active");


$PageBody = <<<PAGE
<main>
    <div class="row">
        <div class="col-sm-12" style="height: auto;">
            <div class="order-products-info">
                <span><h4>היסטוריה</h4></span>
                  <table id="OrderHistory" class="table table-striped">
                     <thead style="background: rgba(216,246,210,0.2)">
                       <tr>
                          <th scope="col">מס</th>
                          <th scope="col">תאריך</th>
                          <th scope="col">שעה</th>
                          <th scope="col">סוג</th>
                          <th scope="col">אייפי</th>
                          <th scope="col">משתמש</th>
                          <th scope="col">הודעה</th>
                       </tr>
                     </thead>
                     <tbody>
                         {productHistory}
                     </tbody>
                  </table>
            </div>
        </div>
    </div>      
</main>
PAGE;

$history = "";
$searchArray = array("");
BugOrderSystem::GetLog();
$orderMessage = Message::SearchMessage(BugOrderSystem::$logReadHandlers["db"], $searchArray);

//$orderMessage = Message::SearchMessage(BugOrderSystem::$logReadHandlers["file"], $searchArray);
if (count($orderMessage) > 0) {
    $rowNum = 1;
    foreach ($orderMessage as $message) {
        //message row color
        $property = "";
        //$property = Constant::GetMessageRowClass($message->GetLevel()->getName());

        $history .= <<<HTML
    <tr {$property}>
        <th scope="row">{$rowNum}</th>
        <td {$property}>{$message->GetTime()->format("d/m/Y")}</td>
        <td {$property}>{$message->GetTime()->format("H:i:s")}</td>
        <td {$property}>{$message->GetLevel()->getDesc()}</td>
        <td {$property}>{$message->GetIp()->FormatAddress("{ip}")}</td>
        <td {$property}>{$message->GetUser()}</td>
        <td {$property}>{$message->GetMessage()}</td>
    </tr>
HTML;

        $rowNum++;
    }
}
else {
    $history = <<<HTML
    <tr>
        <td colspan="4">לא קיימת היסטוריה למערכת</td>
    </tr>
HTML;
}

\Services::setPlaceHolder($PageBody, "productHistory", $history);

\Services::setPlaceHolder($GLOBALS["PageTemplate"],"PageBody",$PageBody);
echo $GLOBALS["PageTemplate"];