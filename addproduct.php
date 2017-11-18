<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 23-Sep-17
 * Time: 17:03
 */


namespace BugOrderSystem;

session_start();

require_once "Classes/BugOrderSystem.php";
$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

$orderId = $_GET["orderid"];
$orderObject = Order::GetById($orderId);
$shopObject = Shop::GetById($orderObject->GetShop()->GetId());

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "עריכת פריט");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "ordersBoardClass", "'current'");
///

$PageTemplate .= <<<PAGE
<main>
    <div class="wrapper">
        <div id="new-order">
            <form class="new-order" method="POST">
                <center>{$orderId} - הוספת פריט להזמנה </center>
                <br>
                שם המוצר<br>
                <input type="text" name="ProductName" required><br>
                ברקוד<br>
                <input type="text" name="ProductBarcode" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                כמות<br>
                <input type="text" name="Quantity" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                <br>
                :הערות למוצר
                <input type="text" name="Remarks" ><br>
                <br>
                <button type="submit" name="addproduct">הוסף מוצר</button>
                <br>
            </form>
        </div>
    </div>
</main>
PAGE;
//setting footer
$PageTemplate .= footer;


//Take form filed and make them variable.

if(isset($_POST['addproduct'])) {
    $product_name = $_POST['ProductName'];
    $product_barcode = $_POST['ProductBarcode'];
    $product_quantity = $_POST['Quantity'];
    $product_remarks = $_POST['Remarks'];

    if(!empty($product_name) && !empty($product_barcode) && !empty($product_quantity)) {
        try {
            $orderProductsObject = new OrderProducts($orderObject->GetId(), $product_name, $product_barcode, $product_remarks, $product_quantity);
            header("Location: vieworder.php?id=$orderId");
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "אנא מלא את כל השדות הנדרשים";
    }
}


echo $PageTemplate;
?>