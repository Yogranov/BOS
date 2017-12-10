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
\Services::setPlaceHolder($PageTemplate, "mainPageClass", "active");
///


$PageTemplate .= <<<PAGE
      <main>
        </div>
        <div class="container" style="margin-top: 20px">
            <h2 style="text-align: center">הזמנה {$orderObject->GetId()}</h2>
            <div class="row">
                <div class="col-sm-6" style="height: 250px;">
                    <div class="order-info">    
                        <span><h4> פרטי הזמנה </h4></span>
                            <ul>                     
                                <li><span> תאריך פתיחה: </span> {$orderInfo->GetTimeStamp()->format("d/m/y H:i")}</li>
                                <li><span> מוכרן: </span> {SellerName}</li>      
                                <li><span> הערות להזמנה: </span> {$orderInfo->GetRemarks()}</li>
                         <li><span> סטאטוס הזמנה: </span> {$orderInfo->GetStatus()->getDesc()}</li>

                                <li><span>עדכון אחרון:</span> {$orderInfo->GetUpdateTime()->format("d/m/y H:i")}</li>
                           </ul> 
                    </div>
                </div>
                <div class="col-sm-6" style="height: 250px;">
                    <div class="order-client-info">
                        <span><h4> פרטי לקוח </h4></span>
                             <ul>
                               <li><span> שם הלקוח:</span> {$orderInfo->GetClient()->GetFullName()}</li>
                               <li><span> פלאפון:</span> {$orderInfo->GetClient()->GetPhoneNumber()}</li>
                               <li><span> לקוח מעוניין בעדכון ע"י אימייל:</span>&nbsp;<span style="font-weight: normal" id="ClientWantEmails" data-value="{clientWantsEmailsBool}">{ClientWantsEmails}</span></li>
                               <li><span> אימייל:</span>    {$orderInfo->GetClient()->GetEmail()}</li>
                            </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="height: auto;">
                    <div class="order-products-info">
                        <span><h4> רשימת מוצרים </h4></span>
                          <table class="table table-striped">
                             <thead style="background: rgba(216,246,210,0.2)">
                               <tr>
                                  <th>שם המוצר</th>
                                  <th>כמות</th>
                                  <th>ברקוד</th>
                                  <th>הערות</th>
                                  <th>תאריך</th>
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
//setting footer
$PageTemplate .= footer;


///echo כן or לא if the client wants mails
if ($orderInfo->GetClient()->IsWantEmail()) {
    $wantEmail =  'כן';
    $wantEmailBool = 1;
} else {
    $wantEmail = 'לא';
    $wantEmailBool = 0;
}
\Services::setPlaceHolder($PageTemplate, "ClientWantsEmails", $wantEmail);
\Services::setPlaceHolder($PageTemplate, "clientWantsEmailsBool", $wantEmailBool);
///

$productRow = <<<EOF
<tr>
    <td>{productName}</td>
    <td>{productQuantity}</td>
    <td>{productBarcode}</td>
    <td>{productRemarks}</td>
    <td>{productTimestamp}</td>
</tr>
EOF;

$productList = "";
foreach ($orderObject->GetOrderProducts() as $product) {
    $productList .= $productRow;
    \Services::setPlaceHolder($productList, "productName", $product->getProductName());
    \Services::setPlaceHolder($productList, "productQuantity", $product->GetQuantity());
    \Services::setPlaceHolder($productList, "productBarcode", $product->GetProductBarcode());
    \Services::setPlaceHolder($productList, "productRemarks", $product->GetRemarks());
    \Services::setPlaceHolder($productList, "productTimestamp", $product->GetTimestamp()->format("d/m/Y"));
}
\Services::setPlaceHolder($PageTemplate, "productsList", $productList);


try {
    $orderSeller = $orderObject->GetSeller()->GetFullName();
    \Services::setPlaceHolder($PageTemplate, "SellerName", $orderSeller);
} catch (\Exception $e) {
    $errorMsg = $e->getMessage();
    \Services::setPlaceHolder($PageTemplate, "SellerName", "מוכר לא ידוע");
}


echo $PageTemplate;


?>


<!--

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
-->
