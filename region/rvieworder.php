<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 30-Oct-17
 * Time: 12:58
 */

namespace BugOrderSystem;

session_start();
require_once "../Classes/BugOrderSystem.php";



$regionId = $_SESSION["RegionId"];

if(!isset($regionId)) {
    header("Location: ../login.php");
}


$orderId = $_GET["id"];
$orderObject = Order::GetById($orderId);

$regionObj = Region::GetById($regionId);


$shopId = $orderObject->GetShop()->GetId();
$shopObject = Shop::GetById($shopId);

$orderInfo = Order::GetById($orderId);
$clientObject = Order::GetById($orderId)->GetClient();

//select order status

//setting header
require_once "Rheader.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הזמנה: ".$_GET["id"]);
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "regionName", $regionObj->GetManager()->GetFirstName());
\Services::setPlaceHolder($PageTemplate, "mainPageClass", "'current'");
///


$PageTemplate .= <<<PAGE
      <main>
        <div id="dialog-confirm" title="לשלוח אימייל ללקוח?" style="display: none; direction: rtl; float: right;">
            <p>
                <span class="ui-icon ui-icon-info" style="float:right; margin:12px 12px 20px 0; direction: rtl;"></span>
                לחיצה על כפתור שליחת האימייל מטה תודיע ללקוח על הגעת המוצרים!<br/>
                שים לב! פעולה זו אינה הפיכה!
            </p>
        </div>

        <div id="view-order" dir="rtl">
          <div class="order-title">הזמנת לקוח - $orderId</div>
          <button class="goBack">לך אחורה</button>
          <br>
          <div id="order-client-info">
          <br>
           <span> פרטי לקוח: </span>
           <ul>
               <li><span> שם הלקוח:</span> {$orderInfo->GetClient()->GetFullName()}</li>
               <li><span> פלאפון:</span> {$orderInfo->GetClient()->GetPhoneNumber()}</li>
               <li><span> לקוח מעוניין בעדכון ע"י אימייל:</span>&nbsp;<span id="ClientWantEmails" data-value="{clientWantsEmailsBool}">{ClientWantsEmails}</span></li>
               <li><span> אימייל:</span>    {$orderInfo->GetClient()->GetEmail()}</li>
            </ul>
            <br>
           </div>
           <div id="order-order-info">
             <br>
             <span> פרטי הזמנה: </span>
             <ul>                     
                 <li><span> תאריך פתיחה: </span> {$orderInfo->GetTimeStamp()->format("d.m.y H:i")}</li>
                 
                 <li><span> מוכרן: </span> {SellerName}</li>
                              
                 <li><span> הערות להזמנה: </span> {$orderInfo->GetRemarks()}</li>
                 
                 <li><span> סטאטוס הזמנה: </span> {$orderInfo->GetStatus()->getDesc()}</li>

                 <br>
                 <li><span>עדכון אחרון:</span> {$orderInfo->GetUpdateTime()->format("d.m.y H:i")}</li>
             </ul> 
             <br>
           </div>
            <div id="order-products-info">
                <br>
                <span> מוצרים: </span> <br> 
                <ul>
                    <br>         
                    {producsList}
                    <br>
                </ul>
                
            </div>
        </div>
    </div> 
</main>
PAGE;
//setting footer
$PageTemplate .= footer;




$orderStatusString = "";
foreach (EOrderStatus::toArray() as $status) {
    $orderStatusString .= "<option value='".$status[0]."' ";
    if ($orderObject->GetStatus()->getValue() == $status[0]){
        $orderStatusString .= "selected='selected'";}
    $orderStatusString .= ">".$status[1]."</option>";

}
\Services::setPlaceHolder($PageTemplate, "orderStatusEnum", $orderStatusString);



if ($orderInfo->GetClient()->IsWantEmail()) {
    $wantEmail =  'כן';
    $wantEmailBool = 1;
} else {
    $wantEmail = 'לא';
    $wantEmailBool = 0;
}
\Services::setPlaceHolder($PageTemplate, "ClientWantsEmails", $wantEmail);
\Services::setPlaceHolder($PageTemplate, "clientWantsEmailsBool", $wantEmailBool);


$producsList = <<<LIST
<tr onclick="document.location = 'editproduct.php?id=$orderId&productId={productId}';">
    <li><span> שם המוצר: </span> {productName}</li>
    <br>         
    <li><span> כמות: </span> {productQuantity}</li>
    <br>         
    <li><span>  ברקוד: </span> {productBarcode}</li>
    <br>         
    <li><span> הערות למוצר: </span> {productRemarks}</li>
    <br>         
    <li><span> תאריך הוספה: </span><span class="glyphicon glyphicon-asterisk" aria-hidden="true" style='color:red'></span> {productTimestamp}</li>
    <br>                         

</tr>
<br>
<br>
<br>                          
LIST;


$allProducts = Order::GetById($orderId)->GetOrderProducts();

$productBoard = (count($allProducts) > 0) ? "" : "<tr colspan='7'>אין מוצרים</tr>";
foreach ($allProducts as $product) {
    $productBoard .= $producsList;
    \Services::setPlaceHolder($productBoard, "productId", $product->GetId());
    \Services::setPlaceHolder($productBoard, "productName", $product->getProductName());
    \Services::setPlaceHolder($productBoard, "productQuantity", $product->GetQuantity());
    \Services::setPlaceHolder($productBoard, "productBarcode", $product->GetProductBarcode());
    \Services::setPlaceHolder($productBoard, "productRemarks", $product->GetRemarks());
    \Services::setPlaceHolder($productBoard, "productTimestamp", $product->GetTimestamp()->format("d.m.y H:i"));
//\Services::setPlaceHolder($productBoard, "productStatus", $product->GetStatus()->getDesc());
    $productId = $product->GetId();
    $productStatusString = "";
    foreach (EProductStatus::toArray() as $status) {
        $productStatusString .= "<option value='".$status[0]."' ";
        if ($product->GetStatus()->getValue() == $status[0]){
            $productStatusString .= "selected='selected'";}
        $productStatusString .= ">".$status[1]."</option>";
    }
    \Services::setPlaceHolder($productBoard, "productStatusEnum", $productStatusString);

}

\Services::setPlaceHolder($PageTemplate, "producsList", $productBoard);


try {
    $orderSeller = $orderObject->GetSeller()->GetFullName();
    \Services::setPlaceHolder($PageTemplate, "SellerName", $orderSeller);
} catch (\Exception $e) {
    $errorMsg = $e->getMessage();
    \Services::setPlaceHolder($PageTemplate, "SellerName", "מוכר לא ידוע");
}


echo $PageTemplate;


if(isset($_POST['orderstatus'])) {
    $order_status = $_POST['orderstatus'];
    $clientWantEmail = $_POST['SendEmail'];

    //Update Status
    if(Order::GetById($orderId)->ChangeStatus($order_status)) {
        if ($clientObject->IsWantEmail() && $clientWantEmail == 1) {
            $subject = "הזמנתך בבאג מחכה לך בסניף {$orderObject->GetShop()->GetShopName()}";
            $message = Constant::EMAIL_CLIENT_ORDER_ARRIVED;
            \Services::setPlaceHolder($message,"Name", $clientObject->GetFullName());
            \Services::setPlaceHolder($message, "ShopName", $shopObject->GetShopName());

            try{
                $clientObject->SendEmail($message, $subject);
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
                echo $errorMsg;
                echo "לא ניתן לשלוח מייל";

            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                echo $errorMsg;
                echo "לא ניתן לשלוח מייל";
            }
        }
    }

    header("Location: Ordersboard.php");
}


foreach ($allProducts as $product) {

    if(isset($_POST['productstatus'.$product->GetId()])) {
        $product_order_status = $_POST['productstatus'.$product->GetId()];
        \Services::dump($product_order_status);
        //Update Product Status
        $productObject = $product->ChangeStatus($product_order_status);
        if($productObject) {
            header("Location: Ordersboard.php");
        }
    }

}

?>