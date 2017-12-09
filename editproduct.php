<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 21-Sep-17
 * Time: 19:13
 */
namespace BugOrderSystem;

session_start();
require_once "Classes/BugOrderSystem.php";

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
$orderId = $_GET["id"];
$productId = $_GET["productId"];
$shopObject = &Shop::GetById($shopId);


//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "עריכת מוצר");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "ordersBoardClass", "'current'");
///


$PageTemplate .= <<<PAGE
<main>
    <div class="container">
        <div id="new-order">

        <form method="POST">
            <center>{productId} - עריכת פריט</center>
                    
            <div class="form-group">
                    <label for="product-name">שם המוצר</label>
                     <input type="text" class="form-control input-sm" name="ProductName" value="{productName}" required><br>
            </div>
   
               <div class="form-group">
                    <label for="product-barcode">ברקוד</label>
            <input type="text" class="form-control input-sm" id="product-barcode" name="ProductBarcode" value="{productBarcode}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
            </div>
            
                        <div class="form-group">
                    <label for="product-quantity">כמות</label>
            <input type="text" class="form-control input-sm" name="Quantity" id="product-quantity" value="{productQuantity}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
            </div>
            
                        <div class="form-group">
                    <label for="product-remarks">הערות למוצר</label>
            <input type="text" class="form-control input-sm" id="product-remarks" name="Remarks" value="{productRemarks}"><br>
            </div>
            
            <input type="submit" value="עדכן פריט" name="editorder" class="btn btn-info btn-block">

            </form>

        </div>
    </div>
</main>
PAGE;
//setting footer
$PageTemplate .= footer;

$productsObject = &Order::GetById($orderId)->GetOrderProducts();
foreach ($productsObject as $id => $productObject){
    if($id == $productId) {
        \Services::setPlaceHolder($PageTemplate, "productId", $productObject->GetId());
        \Services::setPlaceHolder($PageTemplate, "productName", $productObject->getProductName());
        \Services::setPlaceHolder($PageTemplate, "productBarcode", $productObject->GetProductBarcode());
        \Services::setPlaceHolder($PageTemplate, "productQuantity", $productObject->GetQuantity());
        \Services::setPlaceHolder($PageTemplate, "productRemarks", $productObject->GetRemarks());
        $newProductObject = $productObject;
    }
}

//Take form filed and make them variable.
if(isset($_POST['editorder'])) {

    $product_name = $_POST['ProductName'];
    $product_barcode = $_POST['ProductBarcode'];
    $product_remarks = $_POST['Remarks'];
    $product_quantity = $_POST['Quantity'];


    $arrayToUpdate = array(
        "SetProductName" => $_POST['ProductName'],
        "SetProductBarcode" => $_POST['ProductBarcode'],
        "SetRemarks" => $_POST['Remarks'],
        "SetQuantity" => $_POST['Quantity']
    );

        //Update product
    if(!empty($product_name) && !empty($product_barcode) && !empty($product_quantity)) {
        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $newProductObject->$func($attr, false);
            }
            $newProductObject->Update();
            header("Location: vieworder.php?id=$orderId");

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