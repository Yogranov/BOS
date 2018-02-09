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

//setting page title
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageTitle", "הוספה/עריכת מוצר");

//setting menu bar

    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "shopName", $shopObject->GetShopName());
    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "ordersBoardClass", "active");

///

$PageBody = <<<PAGE
<main style="direction: rtl">
    <div class="container">
        <div id="new-edit-product">

            <form method="POST">
                <center>הוספה/עריכת מוצר</center>
                        
                        {infoFlash}
                <div class="form-group">
                        <label for="form-product-barcode">ברקוד</label>
                        <div id="product-barcode"><input type="text" class="form-control" id="form-product-barcode" name="ProductBarcode"></div>
                </div>
                
                <div class="form-group">
                        <label for="form-product-name-edit">שם המוצר</label>
                        <div id="product-name"><input type="text" class="form-control" id="form-product-name-edit" name="ProductName" value=""></div>
                </div>
    
                <div class="form-group">
                        <label for="product-remarks">הערות למוצר</label>
                        <input type="text" class="form-control" id="product-remarks" name="ProductRemarks" value="">
                </div>
                
                <input type="submit" id="form-new-edit-button" value="צור/עדכן מוצר" name="editproduct" class="btn btn-info btn-block">
            </form>

        </div>
    </div>
</main>
PAGE;


\Services::setPlaceHolder($PageBody,"infoFlash","");
try {
    if(isset($_POST["editproduct"])){
        if(!empty($_POST["ProductName"]) && !empty($_POST["ProductBarcode"])) {

            $productName = $_POST["ProductName"];
            $productBarcode = $_POST["ProductBarcode"];
            $productRemarks = $_POST["ProductRemarks"];

            try {
                $productObj = &Products::GetByBarcode($_POST["ProductBarcode"]);

                $arrayToUpdate = array(
                    "SetName" => $productName,
                    "SetRemarks" => $productRemarks
                );

                foreach ($arrayToUpdate as $func => $attr) {
                    $productObj->$func($attr, false);
                }
                $productObj->Update();

            } catch (\Throwable $e){
                //product not exist - trying to create new product
                Products::Add($productBarcode, $productName, $productRemarks);
            }

            //redirecting
            if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
                header("Location: ".$_SESSION["REFERER"]);
            else
                echo "<script>window.location.href = '{$_SESSION["REFERER"]}';</script>";

        } else
            \Services::setPlaceHolder($PageBody,"infoFlash","<center><b>נא למלא את השדות הנדרשים!</b></center>");
    }
} catch(\Throwable $e) {
    \Services::setPlaceHolder($PageBody,"infoFlash",$e->getMessage());
}

\Services::setPlaceHolder($GLOBALS["PageTemplate"],"PageBody", $PageBody);
echo $GLOBALS["PageTemplate"];
?>