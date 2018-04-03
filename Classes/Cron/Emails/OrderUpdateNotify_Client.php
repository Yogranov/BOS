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
use BugOrderSystem\OrderProducts;

$PageName = "/Email/OrderUpdateNotify_Client.php";
require_once __DIR__ . '/../Config.php';

if (!Constants::EMAIL_SEND && !Constants::DEBUG) {
    die();
}

try {
    $logText = $logPrePendText . "החל ריצה אוטומטית";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array(), false, false);

    /** @var OrderProducts[][] $ordersToAlert */
    $ordersToAlert = array();
    Order::LoopAll(function(Order $order) use (&$ordersToAlert) {
        if ($order->GetNotificationEmail() !== null) {
            $proceed = false;
            foreach($order->GetOrderProducts() as $product) {
                if (!$proceed && $product->GetStatus() == EProductStatus::Arrived())
                    $proceed = true;

                if ($product->GetStatus()->getValue() == EProductStatus::Arrived[0] || $product->GetStatus()->getValue() == EProductStatus::Client_Informed[0] || $product->GetStatus()->getValue() == EProductStatus::Message_Sent[0]) {
                    $ordersToAlert[$order->GetId()][] = $product;
                }
            }

            if (!$proceed)
                unset($ordersToAlert[$order->GetId()]);
        }
    });

    $sentEmail = array();
    foreach ($ordersToAlert as $orderId => $productsArray) {
        $orderObject = &Order::GetById($orderId);
        $ClientEmailMessage = \BugOrderSystem\Constant::EMAIL_CLIENT_PRODUCT_ARRIVED;
        Services::setPlaceHolder($ClientEmailMessage, "ShopName", $orderObject->GetShop()->GetShopName());
        Services::setPlaceHolder($ClientEmailMessage, "Name", $orderObject->GetClient()->GetFirstName());
        Services::setPlaceHolder($ClientEmailMessage, "OrderNumber", $orderId);
        Services::setPlaceHolder($ClientEmailMessage, "serverLoc", \BugOrderSystem\Constant::SYSTEM_DOMAIN.\BugOrderSystem\Constant::SYSTEM_SUBFOLDER);

        /** @var OrderProducts[] $arrivedProducts */
        $arrivedProducts = array();
        $number = 1;
        $productsTable = "";
        foreach ($productsArray as $product) {
            $productsTable .= \BugOrderSystem\Constant::EMAIL_CLIENT_PRODUCT_ARRIVED_TABLE;
            Services::setPlaceHolder($productsTable, "ProductName", $product->GetProductName());
            Services::setPlaceHolder($productsTable, "Quantity", $product->GetQuantity());
            Services::setPlaceHolder($productsTable, "Number", $number);
            $number++;

            if ($product->GetStatus() == EProductStatus::Arrived())
                array_push($arrivedProducts, $product);
        }
        Services::setPlaceHolder($ClientEmailMessage, "ClientOrdersList", $productsTable);

        if (Constants::EMAIL_SEND) {
            try {
                $orderObject->SendEmail($ClientEmailMessage, "פריט שהזמנת הגיע לסניף", "", false);
                $sentEmail[$orderId] = $ClientEmailMessage;

                foreach ($arrivedProducts as $product)
                    $product->ChangeStatus(EProductStatus::Message_Sent());

                $logText = $logPrePendText . "נשלח מייל ללקוח {Name} בעל הזמנה {OrderId} עם {ProductNumber} הפריטים שממתינים בסניף";
                \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::DEBUG(), array("Name" => $orderObject->GetClient(), "ProductNumber" => count($productsArray), "OrderId" => $orderObject, "Products" => $productsArray, "Shop" => $orderObject->GetShop(), "EmailMessage" => $ClientEmailMessage),false, false);

            } catch (Throwable $e) {
                //error sending cron emails
                $logText = $logPrePendText . "התרחשה שגיאה בשליחת אימייל ללקוח {Name} בעל הזמנה {OrderId}";
                \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("OrderId" => $orderObject, "Name" => $orderObject->GetClient()),false, false);
            }
        }
    }

    $logText = $logPrePendText . "הסתיימה הריצה - נשלחו {emailNumber} אימיילים ללקוחות";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::NOTICE(), array("emailNumber" => count($sentEmail), "SentEmailsArray" => $sentEmail),false, false);

} catch(Throwable $e) {
    $logText = $logPrePendText . "הריצה לא הצליחה - אירעה שגיאה כללית {ErrorMessage}";
    \BugOrderSystem\BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::ERROR(), array("ErrorMessage" => $e->getMessage(), "ErrorObject" => $e),false, false);
}
