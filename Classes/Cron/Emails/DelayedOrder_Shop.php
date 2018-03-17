<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/17/2018
 * Time: 11:15 AM
 */

use CronJob\Constants;
use BugOrderSystem\Order;
use BugOrderSystem\Shop;
use BugOrderSystem\EOrderStatus;

$PageName = "/Email/DelayedOrder_Shop.php";
require_once __DIR__ . '/../Config.php';

if (!Constants::EMAIL_SEND && !Constants::DEBUG) {
    die();
}

try {
    $logText = $logPrePendText . "החל ריצה אוטומטית";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array(), false, false);

    /** @var Order[][] $ordersToAlert */
    $ordersToAlert = array();
    Order::LoopAll(function(Order $order) use (&$ordersToAlert) {
        $passedDays = Services::DateDiff($order->GetStatusUpdateTimestamp(), "now", "%a");
        if ($passedDays >= \BugOrderSystem\Constant::ORDER_ALERT_DAYS_REGION && $order->GetStatus()->getValue() < EOrderStatus::Delivered[0]) {
            //need to alert the shop about that order (alert time passed and order not collected or aborted)
            $ordersToAlert[$order->GetShop()->GetId()][] = $order;
        }
    });

    $sentEmail = array();
    foreach ($ordersToAlert as $shopId => $shopOrdersArray) {
        $shop = &Shop::GetById($shopId);
        $shopName = $shop->GetShopName();

        $shopEmailMessage = \BugOrderSystem\Constant::EMAIL_SHOP_ORDERS_NEED_ATTENTION;
        Services::setPlaceHolder($shopEmailMessage, "ShopName", $shopName);
        $ordersTable = "";
        $number = 1;
        foreach ($shopOrdersArray as $order) {
            $ordersTable .= \BugOrderSystem\Constant::EMAIL_SHOP_ORDERS_NEED_ATTENTION_TABLE;
            Services::setPlaceHolder($ordersTable, "number", $number);
            Services::setPlaceHolder($ordersTable, "orderId", $order->GetId());
            Services::setPlaceHolder($ordersTable, "lastUpdateTime", $order->GetStatusUpdateTimestamp()->format("d/m/Y H:i:s"));
            Services::setPlaceHolder($ordersTable, "sellerName", $order->GetSeller()->GetFullName());
            $number++;
        }
        Services::setPlaceHolder($shopEmailMessage, "ShopOrdersList", $ordersTable);

        if (Constants::EMAIL_SEND) {
            try {
                $shop->SendEmail($shopEmailMessage, "דוח הזמנות פגות תוקף", "", false);

                $sentEmail[$shop->GetId()] = $shopEmailMessage;

                $logText = $logPrePendText . "נשלח אימייל לחנות {shopName} עם {ordersNumber} הזמנות פגות תוקף";
                \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::DEBUG(), array("shopName" => $shopName, "ordersNumber" => count($shopOrdersArray), "OrderArray" => $shopOrdersArray, "Shop" => $shop),false, false);
            } catch (Throwable $e) {
                //error sending cron emails
                $logText = $logPrePendText . "התרחשה שגיאה בשליחת אימייל לחנות {shopName}";
                \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("shopName" => $shopName, "OrderArray" => $shopOrdersArray, "Shop" => $shop),false, false);
            }
        }
    }

    $logText = $logPrePendText . "הסתיימה הריצה - נשלחו {emailNumber} אימיילים לחנויות עם הזמנות פגות תוקף";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array("emailNumber" => count($sentEmail), "SentEmailsArray" => $sentEmail),false, false);

} catch(Throwable $e) {
    $logText = $logPrePendText . "הריצה לא הצליחה - אירעה שגיאה כללית {ErrorMessage}";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("ErrorMessage" => $e->getMessage(), "ErrorObject" => $e),false, false);
}


