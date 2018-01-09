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

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if ($_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

$orderId = $_GET["orderid"];
$orderObject = &Order::GetById($orderId);
$shopObject = &Shop::GetById($orderObject->GetShop()->GetId());

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "עריכת פריט");
//setting menu bar
$PageTemplate .= headerBody;
$data = "";
if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"])) {
    //setting menu bar
    $data = headerMenu;
    \Services::setPlaceHolder($data, "shopName", $shopObject->GetShopName());
    \Services::setPlaceHolder($data, "ordersBoardClass", "active");
}
\Services::setPlaceHolder($PageTemplate, "HeaderMenu", $data);
///

$PageTemplate .= <<<PAGE
<main>
    <div class="container">
        <div id="new-order">
            <form method="POST">
                <center>הוספת פריט להזמנה - {$orderId}</center>
            <div class="form-group">
                <label for="form-product-barcode">ברקוד</label>
                <input type="text" class="form-control" id="form-product-barcode" placeholder="ברקוד" name="ProductBarcode" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
            </div>
            
            <div class="form-group" id="productName">
                <label for="form-product-name">שם המוצר</label>
                <input type="text" class="form-control" id="form-product-name" placeholder="שם המוצר" name="ProductName" required><br>
            </div>
                
            <div class="form-group">
                <label for="form-product-quantity">כמות</label>
                <input type="text" class="form-control" id="form-product-quantity" placeholder="כמות" value="1" name="Quantity" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
            </div>    
                
             <div class="form-group">
                <label for="form-product-remarks">הערות למוצר</label>
                <input type="text" class="form-control" id="form-product-remarks" placeholder="הערות עבור המוצר" name="Remarks" ><br>
            </div>   
                
            <input type="submit" value="הוסף מוצר" name="addproduct" class="btn btn-info btn-block">

   
                <br>
            </form>
        </div>
    </div>
</main>
PAGE;
//setting footer
if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
    $PageTemplate .= footer;


//Take form filed and make them variable.

if(isset($_POST['addproduct'])) {
    $product_name = $_POST['ProductName'];
    $product_barcode = $_POST['ProductBarcode'];
    $product_quantity = $_POST['Quantity'];
    $product_remarks = $_POST['Remarks'];

    if(!empty($product_name) && !empty($product_barcode) && !empty($product_quantity)) {
        try {
            //$orderProductsObject = new OrderProducts($orderObject->GetId(), $product_name, $product_barcode, $product_remarks, $product_quantity);
            $product = &Products::Add($product_barcode, $product_name);
            $orderObject->AddOrderProduct($product, $product_quantity, $product_remarks);
            if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
                header("Location: ".$_SESSION["REFERER"]);
            else
                echo "<script>window.location.href = '{$_SESSION["REFERER"]}';</script>";
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