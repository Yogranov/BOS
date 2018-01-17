<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 1/16/2018
 * Time: 10:49 PM
 */

namespace BugOrderSystem;
require "Classes/BugOrderSystem.php";

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

try {
    $inserted = array();
    Order::LoopAll(function(Order $order) use (&$inserted) {
        foreach ($order->GetOrderProducts() as $product) {
            BugOrderSystem::GetDB()->where("OrderId", $order->GetId())->where("ProductBarcode", $product->GetProductBarcode())->getOne("orderproducts");
            if (BugOrderSystem::GetDB()->count == 0) {
                $insertArray = array(
                    "OrderId" => $order->GetId(),
                    "ProductName" => $product->GetProductName(),
                    "ProductBarcode" => $product->GetProductBarcode(),
                    "Remarks" => $product->GetRemarks(),
                    "Quantity" => $product->GetQuantity(),
                    "Status" => $product->GetStatus()->getValue()
                );
                BugOrderSystem::GetDB()->insert("orderproducts", $insertArray);

                array_push($inserted, $insertArray);
            }
        }
    });

    \Services::dump($inserted);

} catch(\Throwable $e) {
    echo $e->getMessage();
}