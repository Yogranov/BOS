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

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if ($_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
$orderId = $_GET["orderId"];
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
                          <th>מס</th>
                          <th>תאריך</th>
                          <th>שעה</th>
                          <th>הודעה</th>
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
        $history .= <<<HTML
    <tr>
        <td>{$rowNum}</td>
        <td>{$message->GetTime()->format("d/m/Y")}</td>
        <td>{$message->GetTime()->format("H:i:s")}</td>
        <td>{$message->GetMessage()}</td>
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