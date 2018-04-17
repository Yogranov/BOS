<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 18-Sep-17
 * Time: 17:18
 */
namespace BugOrderSystem;

session_start();
@ob_start();
require_once "Classes/BugOrderSystem.php";

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}


$shopObject = &Shop::GetById($shopId);
$orderId = $_GET["id"];
$orderInfo = &Order::GetById($orderId);
$clientObject = $orderInfo->GetClient();
$clientExtendPhoneNumber = substr_replace(substr_replace($clientObject->GetPhoneNumber(), '-' , 3,0),'-',7,0);

//select order status
$orderObject = $orderInfo;

//setting header
require_once "Header.php";
//setting page title
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageTitle", "הזמנה: ".$_GET["id"]);

if ((is_bool($_GET["ShowHeaderFooter"]) && $_GET["ShowHeaderFooter"] == 1) || !isset($_GET["ShowHeaderFooter"])) {
    //setting menu bar
    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageMenu", $GLOBALS["UserMenu"]);
    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "shopName", $shopObject->GetShopName());
    \Services::setPlaceHolder($GLOBALS["PageTemplate"], "ordersBoardClass", "active");
}

if (isset($_REQUEST["productBarcode"])) {
    $productBarcode = $_REQUEST["productBarcode"];
    $productStatus = $_REQUEST[$productBarcode];

    $orderProductArray = $orderObject->GetOrderProducts();
    $newStatus = EProductStatus::search($productStatus);

    if ($orderProductArray[$productBarcode]->ChangeStatus($newStatus)) {
        if ($newStatus == EProductStatus::Arrived()) {
            if ($orderObject->GetStatus() == EOrderStatus::Arrived()) {
                if ($orderObject->GetNotificationEmail() !== null)
                    $confType = "dialog-EmailConfirm";
                else
                    $confType = "dialog-ManualConfirm";

                $ApiUrl = Constant::API_URL.'?method=OrderInformClient&data='.$orderObject->GetId();
                echo "<span id='InformClient' data-ApiUrl = '{$ApiUrl}' data-confirmationType = '{$confType}' data-orderId = '{$orderObject->GetId()}' style='display: none;'></span>";
            }
        }
        else {
            if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"])) {
                //Not in dialog
                header("Location: Ordersboard.php");
            }
            else {
                //in dialog
                echo "<script>window.location.href = 'vieworder.php?id={$orderId}&ShowHeaderFooter=0';</script>";
            }
        }
    }
}
else if(isset($_REQUEST["SetAsClientInformed"])) {
    foreach ($orderObject->GetOrderProducts() as $productBarcode => $productObject) {
        if ($productObject->GetStatus()->getValue() == EProductStatus::Arrived[0] || $productObject->GetStatus()->getValue() == EProductStatus::Message_Sent[0]) {
            $productObject->ChangeStatus(EProductStatus::Client_Informed());
        }
    }
}
else if(isset($_REQUEST["SetAsProductsOrdered"])) {
    foreach ($orderObject->GetOrderProducts() as $productBarcode => $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Created()) {
            $productObject->ChangeStatus(EProductStatus::Ordered());
        }
    }
}
else if(isset($_REQUEST["SetAsProductsDelivered"])) {
    foreach ($orderObject->GetOrderProducts() as $productBarcode => $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Client_Informed() || $productObject->GetStatus()->getValue() == EProductStatus::Message_Sent[0]) {
            $productObject->ChangeStatus(EProductStatus::Delivered());
        }
    }
}


$pageBody = <<<PAGE
      <main id="ViewOrder">
        <div id="dialog-EmailConfirm" title="לשלוח אימייל ללקוח?" style="display: none; direction: rtl; float: right;">
            <p>
                <span class="ui-icon ui-icon-info" style="float:right; margin:12px 12px 20px 0; direction: rtl;"></span>
                לחיצה על כפתור אישור מטה תודיע ללקוח באימייל על הגעת כלל המוצרים!<br/>
                שים לב! פעולה זו אינה הפיכה!
            </p>
        </div>
        <div id="dialog-ManualConfirm" title="הודעה אישית ללקוח" style="display: none; direction: rtl; float: right;">
            <p>
                <span class="ui-icon ui-icon-info" style="float:right; margin:12px 12px 20px 0; direction: rtl;"></span>
                <br/>כלל הפריטים הגיעו! נא לעדכן את הלקוח טלפונית!
            </p>
        </div>
        <!-- <div class="container" style="margin-top: 20px"> -->
        <div class="container">
            <h2 style="text-align: center">הזמנה {$orderObject->GetId()}</h2>
            <div class="row">
                <div class="col-sm-6">
                    <div class="order-info">    
                        <span><h4 class="bold underline"> פרטי הזמנה </h4></span>
                            <div class="btn btn-info" style="float: left; margin: -34px 0 0 3px;" onclick="document.location ='orderHistory.php?orderId={$orderId}&ShowHeaderFooter=0';">היסטוריה</div>
                            <ul>                     
                                <li><span> תאריך פתיחה: </span> {$orderInfo->GetTimeStamp()->format("d/m/y H:i")}</li>
                                <li><span> מוכרן: </span> {SellerName}</li>      
                                <li><span> הערות להזמנה: </span> {OrderRemarks}</li>
                                <li><span>סטטוס הזמנה מחושב:</span> {OrderStatus}</li>
                           </ul> 
                        <div class="btn btn-primary" style="float: left; margin: -34px 0 0 3px;" onclick="document.location ='editorder.php?orderId={$orderId}&ShowHeaderFooter=0';">ערוך הזמנה</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="order-client-info">
                        <span><h4 class="bold underline">פרטי התקשרות</h4></span>
                             <ul>
                               <li><span> שם הלקוח:</span> {$orderInfo->GetClient()->GetFullName()}</li>
                               <li><span> פלאפון:</span> {$clientExtendPhoneNumber}</li>
                               <li id="order-email"><span> אימייל:</span> <span class="editable"><input type='hidden' name='order_Email' data-orderId = "{$orderId}" data-function = "ChangeNotificationEmail" data-OldValue="{ClientEmail}" value='{ClientEmail}'><span>{ClientEmail}</span></span></li>
                            </ul>
                         <!--<div class="btn btn-primary" style="float: left; margin: 0 0 0 3px;" onclick="document.location ='editclient.php?clientId={$orderInfo->GetClient()->GetId()}&ShowHeaderFooter=0';">ערוך לקוח </div> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="height: auto;">
                    <div class="order-products-info">
                        <span><h4> רשימת מוצרים 
                            <span class="btn btn-primary" onclick="document.location = 'addproduct.php?orderid={$orderId}&ShowHeaderFooter=0';"> הוסף מוצר </span>
                            {ClientInformedButton}
                            {ProductsOrderedButton}
                            {ProductsDeliveredButton}
                        </h4></span>
                          <table id="viewOrderProducts" class="table table-striped">
                             <thead style="background: rgba(216,246,210,0.2)">
                               <tr>
                                  <th>שם המוצר</th>
                                  <th>כמות</th>
                                  <th>ברקוד</th>
                                  <th>סטטוס</th>
                                  <th>הערות</th>
                               </tr>
                             </thead>
                             <tbody>
                                 {productsList}
                             </tbody>
                          </table>
                    </div>
                </div>
            </div>      
        </div>
</main>
PAGE;

$orderRemarks = $orderObject->GetRemarks();
/*
if (empty($orderRemarks))
    $orderRemarks = "ללא";
*/
\Services::setPlaceHolder($pageBody, "OrderRemarks", $orderRemarks);

$clientEmail = $orderObject->GetNotificationEmail();
if (empty($clientEmail))
    $clientEmail = "לא הוזן";
\Services::setPlaceHolder($pageBody, "ClientEmail", $clientEmail);

//set seller name - can be change or delete
try {
    $orderSeller = $orderObject->GetSeller()->GetFullName();
    \Services::setPlaceHolder($pageBody, "SellerName", $orderSeller);
} catch (\Exception $e) {
    $errorMsg = $e->getMessage();
    \Services::setPlaceHolder($pageBody, "SellerName", "מוכר לא ידוע");
}
///

///order status
$orderStatusString = $orderObject->GetStatus()->getDesc();
\Services::setPlaceHolder($pageBody, "OrderStatus", $orderStatusString);
//


//*********************************************{product operations Buttons}******************************************//
//Show ClientInformed Button according to status
$ClientInformedButtonText = "";
if($orderObject->GetStatus() < EOrderStatus::Client_Informed()) {
    foreach ($orderObject->GetOrderProducts() as $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Arrived() || $productObject->GetStatus() == EProductStatus::Message_Sent()) {
            //check if at least one product arrived and show inform button
            $ClientInformedButtonText = "<span class='btn btn-warning' id='ClientInformed' data-SubmitPage = 'vieworder.php?id={$orderObject->GetId()}&ShowHeaderFooter=0&SetAsClientInformed=1' data-orderId='{$orderObject->GetId()}'> לקוח מעודכן </span>";
            break;
        }
    }
}
\Services::setPlaceHolder($pageBody, "ClientInformedButton", $ClientInformedButtonText);

//Show Products Ordered Button if there are products that need to be ordered
$ProductsOrderedButtonText = "";
if($orderObject->GetStatus() == EOrderStatus::Open()) {
    foreach ($orderObject->GetOrderProducts() as $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Created()) {
            //check if at least one product need to be ordered and show that button
            $ProductsOrderedButtonText = "<span class='btn btn-success' id='ProductsOrdered' data-SubmitPage = 'vieworder.php?id={$orderObject->GetId()}&ShowHeaderFooter=0&SetAsProductsOrdered=1' data-orderId='{$orderObject->GetId()}'> המוצרים הוזמנו </span>";
            break;
        }
    }
}
\Services::setPlaceHolder($pageBody, "ProductsOrderedButton", $ProductsOrderedButtonText);

//Show Products Delivered Button if there are products that need to be Deliver
$ProductsDeliveredButtonText = "";
if($orderObject->GetStatus() !== EOrderStatus::Delivered()) {
    foreach ($orderObject->GetOrderProducts() as $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Client_Informed() || $productObject->GetStatus() == EProductStatus::Message_Sent()) {
            //check if at least one product need to be deliver and show that button
            $ProductsDeliveredButtonText = "<span class='btn btn-info' id='ProductsDelivered' data-SubmitPage = 'vieworder.php?id={$orderObject->GetId()}&ShowHeaderFooter=0&SetAsProductsDelivered=1' data-orderId='{$orderObject->GetId()}'> המוצרים נאספו </span>";
            break;
        }
    }
}
\Services::setPlaceHolder($pageBody, "ProductsDeliveredButton", $ProductsDeliveredButtonText);
//*********************************************{End product operations Buttons}******************************************//

$productRow = <<<EOF
<tr style="cursor: default;" data-ProductBarcode="{productBarcode}" data-orderId="{$orderId}">
    <td><span>{productName}</span></td>
    <td class='editable'><input type='hidden' name='product_{productBarcode}_Quantity' data-function = "SetQuantity" data-OldValue="{productQuantity}" value='{productQuantity}'><span>{productQuantity}</span></td>
    <td><span>{productBarcode}</span></td>
    <td><span>
        <form method="POST" id="changeProductStatus_{productBarcode}" name="changeProductStatus_{productBarcode}">
            <input type="hidden" name="productBarcode" id="productBarcode" value={productBarcode}>
            <select class="productstatus" name="{productBarcode}" data-OrderId="{$orderObject->GetId()}">
                {productStatusOptions}
            </select>
        </form>
        </span>
    </td>
    <td class="editable"><input type='hidden' name='product_{productBarcode}_Remarks' data-function = "SetRemarks" data-OldValue="{productQuantity}" value='{productRemarks}'><span>{productRemarks}</span></td>
</tr>
EOF;
$productList = "";
foreach ($orderObject->GetOrderProducts() as $product) {
    $productList .= $productRow;
    \Services::setPlaceHolder($productList, "productName", $product->getProductName());
    \Services::setPlaceHolder($productList, "productQuantity", $product->GetQuantity());
    \Services::setPlaceHolder($productList, "productBarcode", $product->GetProductBarcode());

    $productStatusString = "";
    foreach (EProductStatus::toArray() as $statusName => $status) {
        $productStatusString .= "<option value='".$status[0]."' ";
        if ($product->GetStatus()->getValue() == $status[0])
            $productStatusString .= "selected='selected'";
        if (in_array($statusName, Constant::PRODUCTS_STATUS_NOT_SELECTABLE))
            $productStatusString .= " disabled";
        $productStatusString .= ">".$status[1]."</option>";
    }
    \Services::setPlaceHolder($productList, "productStatusOptions", $productStatusString);

    $remarks = $product->GetRemarks();
    \Services::setPlaceHolder($productList, "productRemarks", $remarks);

    \Services::setPlaceHolder($productList, "editProduct","<a href=\"editproduct.php?id={$orderId}&productBarcode={$product->GetProductBarcode()}&ShowHeaderFooter=0\"><img src=\"images/icons/edit.png\"  height='30px' style='cursor: pointer'></a>");
}
\Services::setPlaceHolder($pageBody, "productsList", $productList);


\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageBody", $pageBody);
echo $GLOBALS["PageTemplate"];

?>