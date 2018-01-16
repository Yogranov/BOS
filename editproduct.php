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

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if ($_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
/*
$productBarcode = $_REQUEST["barcode"];
$productObj = &Products::GetByBarcode($productBarcode);
*/
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
            <center>עריכת מוצר - </center>
                    
            <div class="form-group">
                    <label for="product-barcode">נא להכניס ברקוד לעריכה:</label>
                    <input type="text" class="form-control" id="product-barcode" name="ProductBarcode"><br>
            </div>
                    
            <div class="form-group">
                    <label for="product-name">שם המוצר</label>
                    <input type="text" class="form-control" id="product-name" name="ProductName" value=""><br>
            </div>

            <div class="form-group">
                    <label for="product-remarks">הערות למוצר</label>
                    <input type="text" class="form-control" id="product-remarks" name="ProductRemarks" value=""><br>
            </div>
            
            <input type="submit" value="עדכן מוצר" name="editproduct" class="btn btn-info btn-block">

            </form>

        </div>
    </div>
</main>
PAGE;
//setting footer

if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
    $PageTemplate .= footer;




//Take form filed and make them variable.
if(isset($_POST['editproduct'])) {

    $productObj = &Products::GetByBarcode($_POST["ProductBarcode"]);

    $arrayToUpdate = array(
        "SetName" => $_POST['ProductName'],
        "SetRemarks" => $_POST['ProductRemarks']
    );

        //Update product
    if(!empty($_POST['ProductName'] && !empty($_POST['editproduct']))) {
        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $productObj->$func($attr, false);
            }
            $productObj->Update();

        }
        catch (Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות";
    }
}

echo $PageTemplate;


?>