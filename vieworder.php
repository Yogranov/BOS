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
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הזמנה: ".$_GET["id"]);

$PageTemplate .= headerBody;
$data = "";
if ((is_bool($_GET["ShowHeaderFooter"]) && $_GET["ShowHeaderFooter"] == 1) || !isset($_GET["ShowHeaderFooter"])) {
    //setting menu bar
    $data = headerMenu;
    \Services::setPlaceHolder($data, "shopName", $shopObject->GetShopName());
    \Services::setPlaceHolder($data, "ordersBoardClass", "active");
}
\Services::setPlaceHolder($PageTemplate, "HeaderMenu", $data);



if (isset($_REQUEST["ProductId"])) {
    $productId = $_REQUEST["ProductId"];
    $productStatus = $_REQUEST["productstatus_" . $productId];

    $orderProductArray = $orderObject->GetOrderProducts();
    $newStatus = EProductStatus::search($productStatus);

    if ($orderProductArray[$productId]->ChangeStatus($newStatus)) {
        if ($newStatus == EProductStatus::Arrived()) {
            $arrivedCount = 0;
            $allOrderProducts = $orderObject->GetOrderProducts();
            foreach ($orderObject->GetOrderProducts() as $innerProductId => $productObject) {
                if ($productObject->GetStatus() == EProductStatus::Arrived())
                    $arrivedCount++;
            }

            if ($arrivedCount == count($allOrderProducts)) {
                if ($orderObject->GetClient()->IsWantEmail())
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
    foreach ($orderObject->GetOrderProducts() as $productId => $productObject) {
        if ($productObject->GetStatus()->getValue() == EProductStatus::Arrived[0]) {
            $productObject->ChangeStatus(EProductStatus::Client_Informed());
        }
    }
}
else if(isset($_REQUEST["SetAsProductsOrdered"])) {
    foreach ($orderObject->GetOrderProducts() as $productId => $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Created()) {
            $productObject->ChangeStatus(EProductStatus::Ordered());
        }
    }
}
else if(isset($_REQUEST["SetAsProductsDelivered"])) {
    foreach ($orderObject->GetOrderProducts() as $productId => $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Client_Informed()) {
            $productObject->ChangeStatus(EProductStatus::Delivered());
        }
    }
}


$PageTemplate .= <<<PAGE
      <main>
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
                <div class="col-sm-6" style="height: 250px;">
                    <div class="order-info">    
                        <span><h4> פרטי הזמנה </h4></span>
                            <ul>                     
                                <li><span> תאריך פתיחה: </span> {$orderInfo->GetTimeStamp()->format("d/m/y H:i")}</li>
                                <li><span> מוכרן: </span> {SellerName}</li>      
                                <li><span> הערות להזמנה: </span> {OrderRemarks}</li>
                                <li><span>סטטוס הזמנה מחושב:</span> {OrderStatus}</li>
                           </ul> 
                        <div class="btn btn-primary" style="float: left; margin: -34px 0 0 3px;" onclick="document.location ='editorder.php?orderId={$orderId}&ShowHeaderFooter=0';">ערוך הזמנה </div>
                    </div>
                </div>
                <div class="col-sm-6" style="height: 250px;">
                    <div class="order-client-info">
                        <span><h4> פרטי לקוח </h4></span>
                             <ul>
                               <li><span> שם הלקוח:</span> {$orderInfo->GetClient()->GetFullName()}</li>
                               <li><span> פלאפון:</span> {$clientExtendPhoneNumber}</li>
                               <li><span> לקוח מעוניין בעדכון ע"י אימייל:</span>&nbsp;<span style="font-weight: normal" id="ClientWantEmails" data-value="{clientWantsEmailsBool}">{ClientWantsEmails}</span></li>
                               <li><span> אימייל:</span>    {ClientEmail}</li>
                            </ul>
                         <div class="btn btn-primary" style="float: left; margin: 3px;" onclick="document.location ='editclient.php?clientId={$orderInfo->GetClient()->GetId()}&ShowHeaderFooter=0';">ערוך לקוח </div>
                         <!-- <div class="btn btn-primary" style="float: left; margin: 3px;" data-action="OpenBOSDialog" data-page="editclient.php" data-dialogTitle="עריכת לקוח" data-variables="clientId={$orderInfo->GetClient()->GetId()}&ShowHeaderFooter=0">ערוך לקוח </div> -->
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
                          <table class="table table-striped">
                             <thead style="background: rgba(216,246,210,0.2)">
                               <tr>
                                  <th>שם המוצר</th>
                                  <th>כמות</th>
                                  <th>ברקוד</th>
                                  <th>סטטוס</th>
                                  <th>הערות</th>
                                  <th>תאריך</th>
                                  <th></th>
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
\Services::setPlaceHolder($PageTemplate, "OrderRemarks", $orderRemarks);

$clientEmail = $orderObject->GetClient()->GetEmail();
if (empty($clientEmail))
    $clientEmail = "לא הוזן";
\Services::setPlaceHolder($PageTemplate, "ClientEmail", $clientEmail);

//set seller name - can be change or delete
try {
    $orderSeller = $orderObject->GetSeller()->GetFullName();
    \Services::setPlaceHolder($PageTemplate, "SellerName", $orderSeller);
} catch (\Exception $e) {
    $errorMsg = $e->getMessage();
    \Services::setPlaceHolder($PageTemplate, "SellerName", "מוכר לא ידוע");
}
///

///order status
$orderStatusString = $orderObject->GetStatus()->getDesc();
\Services::setPlaceHolder($PageTemplate, "OrderStatus", $orderStatusString);
//


//set if the client wants email or not
if ($orderInfo->GetClient()->IsWantEmail()) {
    $wantEmail =  'כן';
    $wantEmailBool = 1;
} else {
    $wantEmail = 'לא';
    $wantEmailBool = 0;
}
\Services::setPlaceHolder($PageTemplate, "ClientWantsEmails", $wantEmail);
\Services::setPlaceHolder($PageTemplate, "clientWantsEmailsBool", $wantEmailBool);

//*********************************************{product operations Buttons}******************************************//
//Show ClientInformed Button according to status
$ClientInformedButtonText = "";
if($orderObject->GetStatus() < EOrderStatus::Client_Informed()) {
    foreach ($orderObject->GetOrderProducts() as $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Arrived()) {
            //check if at least one product arrived and show inform button
            $ClientInformedButtonText = "<span class='btn btn-warning' id='ClientInformed' data-SubmitPage = 'vieworder.php?id={$orderObject->GetId()}&ShowHeaderFooter=0&SetAsClientInformed=1' data-orderId='{$orderObject->GetId()}'> לקוח מעודכן </span>";
            break;
        }
    }
}
\Services::setPlaceHolder($PageTemplate, "ClientInformedButton", $ClientInformedButtonText);

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
\Services::setPlaceHolder($PageTemplate, "ProductsOrderedButton", $ProductsOrderedButtonText);

//Show Products Delivered Button if there are products that need to be Deliver
$ProductsDeliveredButtonText = "";
if($orderObject->GetStatus() !== EOrderStatus::Delivered()) {
    foreach ($orderObject->GetOrderProducts() as $productObject) {
        if ($productObject->GetStatus() == EProductStatus::Client_Informed()) {
            //check if at least one product need to be deliver and show that button
            $ProductsDeliveredButtonText = "<span class='btn btn-info' id='ProductsDelivered' data-SubmitPage = 'vieworder.php?id={$orderObject->GetId()}&ShowHeaderFooter=0&SetAsProductsDelivered=1' data-orderId='{$orderObject->GetId()}'> המוצרים נאספו </span>";
            break;
        }
    }
}
\Services::setPlaceHolder($PageTemplate, "ProductsDeliveredButton", $ProductsDeliveredButtonText);
//*********************************************{End product operations Buttons}******************************************//

$onclickEditJS = "onclick=\"document.location = 'editproduct.php?id={$orderId}&productId={productId}&ShowHeaderFooter=0'\"";
$productRow = <<<EOF
<tr style="cursor: pointer;">
    <td {$onclickEditJS}>{productName}</td>
    <td {$onclickEditJS}>{productQuantity}</td>
    <td {$onclickEditJS}>{productBarcode}</td>
    <td>
        <form method="POST" id="changeProductStatus_{productId}" name="changeProductStatus_{productId}">
              <input type="hidden" name="ProductId" id="ProductId" value={productId}>
              <select class="productstatus" name="productstatus_{productId}" data-ProductId="{productId}" data-OrderId="{$orderObject->GetId()}" required>
               {productStatusOptions}
               </select>
        </form>
    </td>
    <td {$onclickEditJS}>{productRemarks}</td>
    <td {$onclickEditJS}>{productTimestamp}</td>
    <td {$onclickEditJS}>{editProduct}</td>
</tr>
EOF;
$productList = "";
foreach ($orderObject->GetOrderProducts() as $product) {
    $productList .= $productRow;
    \Services::setPlaceHolder($productList, "productName", $product->getProductName());
    \Services::setPlaceHolder($productList, "productQuantity", $product->GetQuantity());
    \Services::setPlaceHolder($productList, "productBarcode", $product->GetProductBarcode());

    $productStatusString = "";
    foreach (EProductStatus::toArray() as $status) {
        $productStatusString .= "<option value='".$status[0]."' ";
        if ($product->GetStatus()->getValue() == $status[0]){
            $productStatusString .= "selected='selected'";}
        $productStatusString .= ">".$status[1]."</option>";
    }
    \Services::setPlaceHolder($productList, "productStatusOptions", $productStatusString);

    \Services::setPlaceHolder($productList, "productId", $product->GetId());

    $remarks = $product->GetRemarks();
    \Services::setPlaceHolder($productList, "productRemarks", $remarks);

    \Services::setPlaceHolder($productList, "productTimestamp", $product->GetTimestamp()->format("d/m/Y"));
    \Services::setPlaceHolder($productList, "editProduct","<a href=\"editproduct.php?id={$orderId}&productId={$product->GetId()}&ShowHeaderFooter=0\"><img src=\"images/icons/edit.png\"  height='30px' style='cursor: pointer'></a>");
}
\Services::setPlaceHolder($PageTemplate, "productsList", $productList);

//setting footer
if ((is_bool($_GET["ShowHeaderFooter"]) && $_GET["ShowHeaderFooter"] == 1) || !isset($_GET["ShowHeaderFooter"])) {
    $PageTemplate .= footer;
}

echo $PageTemplate;

?>