<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 04-Oct-17
 * Time: 16:13
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
$orderId = $_GET["orderId"];
$orderObject = &Order::GetById($orderId);
$shopObj = &Shop::GetById($shopId);


//setting header
require_once "Header.php";

$pageBody = <<<PAGE
<main>
    <div class="container">
        <div id="new-order">
            <form class="new-order" method="POST">
                <center>עריכת הזמנה - {$orderId}</center>
                
                <div class="form-group">
                    <label for="order-remarks">הערות להזמנה</label>
                    <input type="text" class="form-control" id="order-remarks" name="remarks" value="{$orderObject->GetRemarks()}"><br>
                </div>
                
                <div class="form-group">
                    <label for="form-seller">מוכרן</label>
                    <select class="form-control" id="form-seller" name="seller">
                    {sellerSelect}
                    </select>
                </div>
                  
                <br>
                <br>
                <br>
                <input type="submit" value="עדכן הזמנה" name="editorder" class="btn btn-info btn-block">

                <br>
            </form>
        </div>
    </div>
</main>
PAGE;


$orderSellersString = "";
foreach ($shopObj->GetActiveSellers() as $sellerId => $sellerObj) {
    $orderSellersString .= "<option value='".$sellerId."' ";
    if ($orderObject->GetSeller()->GetId() === $sellerId){
        $orderSellersString .= "selected='selected'";}
    $orderSellersString .= ">".$sellerObj->GetFullName()."</option>";

}
\Services::setPlaceHolder($pageBody, "sellerSelect", $orderSellersString);




//Take form filed and make them variable.

if(isset($_POST['editorder'])) {

    $remarks = $_POST['remarks'];
    $seller = $_POST['seller'];

    if (!empty($seller)) {

        $arrayToUpdate = array(
            "SetSellerId" => $seller,
            "SetRemarks" => $remarks
        );

        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $orderObject->$func($attr, false);
            }
            $orderObject->Update();
            if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
                header("Location: ".$_SESSION["REFERER"]);
            else
                echo "<script>window.location.href = '{$_SESSION["REFERER"]}';</script>";
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות";
    }
}


\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageBody", $pageBody);
echo $GLOBALS["PageTemplate"];
?>