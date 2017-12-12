<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 30-Oct-17
 * Time: 09:00
 */
namespace BugOrderSystem;

require_once "../Classes/BugOrderSystem.php";

session_start();

$regionId = $_SESSION["RegionId"];
if(!isset($regionId)) {
    header("Location: ../login.php");
}


$regionObj = Region::GetById($regionId);

//setting header
require_once "Rheader.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "ראשי");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "regionName", $regionObj->GetManager()->GetFirstName());
\Services::setPlaceHolder($PageTemplate, "mainPageClass", "active");
///


$PageTemplate .= <<<INDEX
<main>
    <div class="wrapper">
            {notificationListBoard}
        </div>
    
    </div>  
</main>
INDEX;

$notificationBoard = <<<BOARD
<div class="container">
    <div id="notificationBoard">
        <h2 style="text-align: center; margin: -10px 0 0 0; padding-bottom: 10px;">לוח התראות</h2>
                {notificationLists}
    </div>
</div>
BOARD;


$notificationList = <<<List
<ul data-shopId="{ShopId}">
    {InnerList}
</ul>
List;

$notificationInnerList = <<<List
<li data-orderId="{OrderId}">{orderInfo}</li>
List;

$notificationHandler = "";
$hasNotification = false;
foreach ($regionObj->GetShops() as $Shop) {
    $shopObject = &Shop::GetById($Shop->GetId());
    $shopOrders = Order::GetActiveOrders($shopObject);
    $notificationInnerListHandler = "";
    foreach ($shopOrders as $order) {
        $timeDiff = \Services::DateDiff($order->GetTimeStamp(), "now", "%a");
        if($timeDiff >= 14) {
            $hasNotification = true;
            $notificationInnerListHandler .= $notificationInnerList;
            $notification = "הזמנה <a href='rvieworder.php?id={$order->GetId()}'>{$order->GetId()}</a> בסטאטוס '{$order->GetStatus()->getDesc()}' פתוחה כ-{$timeDiff} ימים בסניף {$order->GetShop()->GetShopName()}.";
            \Services::setPlaceHolder($notificationInnerListHandler,"orderInfo", $notification);
            \Services::setPlaceHolder($notificationInnerListHandler,"OrderId", $order->GetId());
        }
    }

    if (!empty($notificationInnerListHandler)) {
        $notificationHandler .= $notificationList;
        \Services::setPlaceHolder($notificationHandler,"InnerList", $notificationInnerListHandler);
        \Services::setPlaceHolder($notificationHandler,"ShopId", $shopObject->GetId());
    }
}
\Services::setPlaceHolder($notificationBoard,"notificationLists", $notificationHandler);

$hasNotification ? \Services::setPlaceHolder($PageTemplate, "notificationListBoard", $notificationBoard) :
    \Services::setPlaceHolder($PageTemplate, "notificationListBoard", "");





//setting footer
$PageTemplate .= footer;

echo $PageTemplate;
