<?php
/**
* Created by PhpStorm.
* User: Yogev
* Date: 13-Nov-17
* Time: 11:23
*/
namespace BugOrderSystem;
require_once "Classes/BugOrderSystem.php";


$orderId = $_GET['orderId'];
$orderObj = Order::GetById($orderId);
$oldStatus = $orderObj->GetStatus()->getDesc();

if (count($orderId) != 0 && $orderObj->GetStatus() != EOrderStatus::Client_Informed()){
        if ($orderObj->GetStatus() == EOrderStatus::Arrived()) {

            foreach ($orderObj->GetOrderProducts() as $productId => $orderProducts) {
                if ($orderProducts->GetStatus() == EProductStatus::Arrived())
                    $orderProducts->ChangeStatus(EProductStatus::Client_Informed());
            }

            //Log the status changing
            $timeNow = new \DateTime( "now", new \DateTimeZone("Asia/Jerusalem"));
            $logFile = fopen("logs/StatusLog.php", "a");
            fwrite($logFile, "\n" . "<br>" . "{$timeNow->format("Y/m/d H:i:s")} - סניף <b>{$orderObj->GetShop()->GetShopName()}</b> - הזמנה מספר <b>{$orderObj->GetId()}</b> עברה מסטאטוס <b>{$oldStatus}</b> לסטאטוס <b>{$orderObj->GetStatus()->getDesc()}</b> אוטומטית, על ידי הלקוח <b>{$orderObj->GetClient()->GetFullName()}</b>.");
            fclose($logFile);
        }
}
