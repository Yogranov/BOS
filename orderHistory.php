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
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "היסטוריית הזמנה");

//setting menu bar
$PageTemplate .= headerBody;
$data = "";
if (is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) {
    //setting menu bar
    $data = headerMenu;
    \Services::setPlaceHolder($data, "shopName", $shopObj->GetShopName());
    \Services::setPlaceHolder($data, "ordersBoardClass", "active");
}
\Services::setPlaceHolder($PageTemplate, "HeaderMenu", $data);

$PageTemplate .= <<<PAGE
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
\Services::setPlaceHolder($PageTemplate, "productHistory", $productHistory);

//setting footer
if (is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"])
    $PageTemplate .= footer;

echo $PageTemplate;

?>