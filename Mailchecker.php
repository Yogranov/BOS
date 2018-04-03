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
$orderObj = &Order::GetById($orderId);

foreach ($orderObj->GetOrderProducts() as $productId => $orderProducts) {
    if ($orderProducts->GetStatus() == EProductStatus::Message_Sent())
        $orderProducts->ChangeStatus(EProductStatus::Client_Informed());
}
