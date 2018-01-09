<?php
/**
 * Created by PhpStorm.
 * User: shale
 * Date: 1/8/2018
 * Time: 5:08 PM
 */

namespace BugOrderSystem;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "Classes/BugOrderSystem.php";

$orderProducts = BugOrderSystem::GetDB()->orderBy("orderId")->get("orderproducts");
if (is_array($orderProducts) && count($orderProducts) > 0) {
    $updatedOrderTable = array();
    $insertedProducts = array();
    foreach ($orderProducts as $product) {
        AddProduct($product, $insertedProducts);
        AddProductsToOrderTable($product, $updatedOrderTable);
    }

    $numUpdatedOrderTable = count($updatedOrderTable);
    if ($numUpdatedOrderTable > 0) {
        echo " הזמנות עודכנו בעמודת הפריטים בבסיס הנתונים בטבלת ההזמנות:</br>{$numUpdatedOrderTable}ל";
        \Services::dump($updatedOrderTable);
    }
    else {
        echo "לא עודכנו הזמנות בבסיס הנתונים!";
    }
    echo "<br><br>";

    $numInsertedProducts = count($insertedProducts);
    if ($numInsertedProducts > 0) {
        echo "{$numInsertedProducts} פריטים הוכנסו לטבלת products";
        \Services::dump($insertedProducts);
    }
    else {
        echo "לא הוכנסו פריטים חדשים לבסיס הנתונים לטבלת product";
    }
    echo "<br><br>";

}

/**
 * @param array $productData
 * @param array $inserted
 * @throws Exception
 */
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
function AddProductsToOrderTable(array $productData, array &$updated){
    $orderProductArray = array();
    $orderProduct = BugOrderSystem::GetDB()->where("OrderId", $productData["OrderId"])->get("orders", null, "products");
    $orderProduct = $orderProduct[0]["products"];
    if (!is_null($orderProduct))
        $orderProductArray = (array)@json_decode($orderProduct);

    $ProductBarcode = $productData["ProductBarcode"];

    if (in_array($ProductBarcode, array_keys($orderProductArray))) {
        return;
    }

    $orderProductArray[$ProductBarcode] = array($productData["Quantity"], $productData["Status"], $productData["Remarks"]);
    $jsonString = @json_encode($orderProductArray);

    $updateData = array(
        "products" => $jsonString
    );
    $success = BugOrderSystem::GetDB()->where("OrderId", $productData["OrderId"])->update("orders", $updateData, 1);
    if ($success !== false)
        $updated[$productData["OrderId"]] = $jsonString;
}