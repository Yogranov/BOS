<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 3/17/2018
 * Time: 1:10 PM
 */

use CronJob\Constants;
use BugOrderSystem\Order;
use BugOrderSystem\EProductStatus;
use BugOrderSystem\Products;

$PageName = "/Email/OrderUpdateNotify_Client.php";
require_once __DIR__ . '/../Config.php';

if (!Constants::EMAIL_SEND && !Constants::DEBUG) {
    die();
}

try {
    $logText = $logPrePendText . "החל ריצה אוטומטית";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array(), false, false);

    /** @var Products[][] $ordersToAlert */
    $ordersToAlert = array();
    Order::LoopAll(function(Order $order) use (&$ordersToAlert) {
        if ($order->GetNotificationEmail() !== null) {
            foreach($order->GetOrderProducts() as $product) {
                if ($product->GetStatus() == EProductStatus::Arrived())
                    $ordersToAlert[$order->GetId()][] = $product;
            }
        }
    });

    Services::dump($ordersToAlert);
    $sentEmail = array();
    foreach ($ordersToAlert as $orderId => $productsArray) {
        $orderObject = &Order::GetById($orderId);
        $shopEmailMessage = \BugOrderSystem\Constant::EMAIL_CLIENT_PRODUCT_ARRIVED;
    }

    $logText = $logPrePendText . "הסתיימה הריצה - נשלחו {emailNumber} אימיילים ללקוחות";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array("emailNumber" => count($sentEmail), "SentEmailsArray" => $sentEmail),false, false);

} catch(Throwable $e) {
    $logText = $logPrePendText . "הריצה לא הצליחה - אירעה שגיאה כללית {ErrorMessage}";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("ErrorMessage" => $e->getMessage(), "ErrorObject" => $e),false, false);
}
