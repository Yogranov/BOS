<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 21-Sep-17
 * Time: 19:13
 */
namespace BugOrderSystem;

session_start();

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once "Classes/BugOrderSystem.php";

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if ($_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

$orderId = $_REQUEST["id"];
$productBarcode = $_REQUEST["productBarcode"];
$shopObject = &Shop::GetById($shopId);


//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "עריכת מוצר");

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

$maxQuantity = Constant::PRODUCT_MAX_QUANTITY;
$PageTemplate .= <<<PAGE
<main>
    <div class="container">
        <div id="new-order">

        <form method="POST">
            <center>עריכת פריט - {productBarcode}</center>
                    
            <div class="form-group">
                    <label for="product-name">שם המוצר</label>
                    <input type="text" class="form-control" name="ProductName" value="{productName}" disabled><br>
            </div>
            <div class="form-group">
                    <label for="product-barcode">ברקוד</label>
                    {productBarcode}
                    <input type="text" class="form-control" id="product-barcode" name="ProductBarcode" value="{productBarcode}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" disabled><br>
            </div>
            <div class="form-group">
                    <label for="product-quantity">כמות</label><output for="product-quantity" id="QuantityOutput">{productQuantity}</output>
                    <!-- <input type="text" class="form-control" name="Quantity" id="product-quantity" value="{productQuantity}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>-->
                    <input type="range" name="Quantity" id="product-quantity" min="1" max="{$maxQuantity}" value="{productQuantity}" step="1" oninput="outputUpdate(value, '#QuantityOutput')" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required>
            </div>
            <div class="form-group">
                    <label for="product-remarks">הערות למוצר</label>
                    <input type="text" class="form-control" id="product-remarks" name="Remarks" value="{productRemarks}"><br>
            </div>
            
            <input type="submit" value="עדכן פריט" name="editorder" class="btn btn-info btn-block">

            </form>

        </div>
    </div>
</main>
PAGE;
//setting footer
if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
    $PageTemplate .= footer;

$productsObject = &Order::GetById($orderId)->GetOrderProducts();
foreach ($productsObject as $barcode => $productObject){
    if($barcode == $productBarcode) {
        \Services::setPlaceHolder($PageTemplate, "productName", $productObject->GetProductName());
        \Services::setPlaceHolder($PageTemplate, "productBarcode", $productObject->GetProductBarcode());
        \Services::setPlaceHolder($PageTemplate, "productQuantity", $productObject->GetQuantity());
        \Services::setPlaceHolder($PageTemplate, "productRemarks", $productObject->GetRemarks());
        $newProductObject = $productObject;
    }
}

//Take form filed and make them variable.
if(isset($_POST['editorder'])) {
    $product_remarks = $_POST['Remarks'];
    $product_quantity = $_POST['Quantity'];

    $arrayToUpdate = array(
        "SetRemarks" => $_POST['Remarks'],
        "SetQuantity" => $_POST['Quantity']
    );

        //Update product
    if(!empty($product_quantity)) {
        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $newProductObject->$func($attr, false);
            }
            $newProductObject->Update();
            if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
                header("Location: ".$_SESSION["REFERER"]);
            else
                echo "<script>window.location.href = '{$_SESSION["REFERER"]}';</script>";

        }catch (Exception $e) {
            //todo: fix the Exeption issue.
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות";
    }
}

echo $PageTemplate;


?>