<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 04-Oct-17
 * Time: 16:13
 */
namespace BugOrderSystem;

use Log\Message;

session_start();
require_once "Classes/BugOrderSystem.php";
//require_once "Classes/Log/Message.php";

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
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageTitle", "היסטוריית הזמנה");

//setting menu bar
    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "shopName", $shopObj->GetShopName());
    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "ordersBoardClass", "active");


$PageBody = <<<PAGE
<main>
    <div class="row">
        <div class="col-sm-12" style="height: auto;">
            <div class="order-products-info">
                <span><h4>היסטורייה</h4></span>
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

$productHistory = "";
$searchArray = array("הזמנה {$orderId}");
BugOrderSystem::GetLog();
$orderMessage = Message::SearchMessage(BugOrderSystem::$logReadHandlers["db"], $searchArray);
$rowNum = 1;
foreach ($orderMessage as $message) {
    $productHistory .= <<<HTML
    <tr>
        <td>{$rowNum}</td>
        <td>{$message->GetTime()->format("d/m/Y")}</td>
        <td>{$message->GetTime()->format("H:i:s")}</td>
        <td>{$message->GetMessage()}</td>
    </tr>
HTML;

    $rowNum++;
}
\Services::setPlaceHolder($PageBody, "productHistory", $productHistory);

\Services::setPlaceHolder($GLOBALS["PageTemplate"],"PageBody",$PageBody);
echo $GLOBALS["PageTemplate"];

?>