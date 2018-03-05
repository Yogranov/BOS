<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 1/8/2018
 * Time: 5:08 PM
 */

namespace BugOrderSystem;
require "Classes/BugOrderSystem.php";

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/*

$orderProducts = BugOrderSystem::GetDB()->orderBy("orderId")->get("orderproducts");
if (is_array($orderProducts) && count($orderProducts) > 0) {
    $updatedOrderTable = array();
    $insertedProducts = array();
    BugOrderSystem::GetDB()->update("orders", array("products" => null)); //clear products column from order table
    foreach ($orderProducts as $product) {
        AddProduct($product, $insertedProducts);
        AddProductsToOrderTable($product, $updatedOrderTable);
    }

    $string = "";
    $numUpdatedOrderTable = count($updatedOrderTable);
    if ($numUpdatedOrderTable > 0) {
        $string .= " הזמנות עודכנו בעמודת הפריטים בבסיס הנתונים בטבלת ההזמנות:</br>{$numUpdatedOrderTable}ל";
        $string .= \Services::dump($updatedOrderTable, false);
    }
    else {
        $string .= "לא עודכנו הזמנות בבסיס הנתונים!";
    }
    $string .= "<br><br>";

    $numInsertedProducts = count($insertedProducts);
    if ($numInsertedProducts > 0) {
        $string .= "{$numInsertedProducts} פריטים הוכנסו לטבלת products";
        $string .= \Services::dump($insertedProducts, false);
    }
    else {
        $string .= "לא הוכנסו פריטים חדשים לבסיס הנתונים לטבלת product";
    }
    $string .= "<br><br>";

    echo $string;

    $logText = "הקובץ DBConversion סיים ריצה - כלל המוצרים סונכרנו (בהזמנות והמוצרים עצמם)";
    BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::DEBUG());
}

/**
 * @param array $productData
 * @param array $inserted
 * @throws Exception
 */

/*
function AddProduct(array $productData, array &$inserted) {
    if (count($inserted) > 0 && array_search($productData["ProductBarcode"], $inserted) !== false)
        return;

    BugOrderSystem::GetDB()->where("Barcode", $productData["ProductBarcode"])->get("products");
    if (BugOrderSystem::GetDB()->count > 0)
        return;

    $dbProductData = array(
        "Barcode" => $productData["ProductBarcode"],
        "Name" => $productData["ProductName"]
    );
    $success = BugOrderSystem::GetDB()->insert("products", $dbProductData);
    if ($success !== false)
        array_push($inserted, $productData["ProductBarcode"]);
}

/**
 * @param array $productData
 * @param array $updated
 * @throws Exception
 */

/*
function AddProductsToOrderTable(array $productData, array &$updated){
    $orderProductArray = array();
    $orderProduct = BugOrderSystem::GetDB()->where("OrderId", $productData["OrderId"])->get("orders", null, "products");
    if (!is_null($orderProduct) && count($orderProduct) > 0) {
        $orderProduct = $orderProduct[0]["products"];
        $orderProductArray = (array)@json_decode($orderProduct);
    }

    $ProductBarcode = (string)$productData["ProductBarcode"];
    $productArray = array($productData["Quantity"], $productData["Status"], $productData["Remarks"]);

    /*
    if (in_array($ProductBarcode, array_keys($orderProductArray))) {
        if ($orderProductArray[$ProductBarcode] === $productArray)
            return;
    }
    */


/*

    $orderProductArray[$ProductBarcode] = $productArray;
    $jsonString = @json_encode($orderProductArray);

    $updateData = array(
        "products" => $jsonString
    );
    $success = BugOrderSystem::GetDB()->where("OrderId", $productData["OrderId"])->update("orders", $updateData, 1);
    if ($success !== false)
        $updated[$productData["OrderId"]] = $jsonString;
}