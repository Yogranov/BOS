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
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הוספה/עריכת מוצר");

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
        <div id="new-edit-product">

            <form method="POST">
                <center>הוספה/עריכת מוצר</center>
                        
                <div class="form-group">
                        <label for="form-product-barcode">ברקוד</label>
                        <div id="product-barcode"><input type="text" class="form-control" id="form-product-barcode" name="ProductBarcode"></div>
                </div>
                
                <div class="form-group">
                        <label for="form-product-name">שם המוצר</label>
                        <div id="product-name"><input type="text" class="form-control" id="form-product-name" name="ProductName" value=""></div>
                </div>
    
                <div class="form-group">
                        <label for="product-remarks">הערות למוצר</label>
                        <input type="text" class="form-control" id="product-remarks" name="ProductRemarks" value="">
                </div>
                
                <input type="submit" id="form-new-edit-button" value="צור מוצר" name="editproduct" class="btn btn-info btn-block">
            </form>

        </div>
    </div>
</main>
PAGE;
//setting footer

if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
    $PageTemplate .= footer;
















echo $PageTemplate;


?>