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


$shopObject = Shop::GetById($shopId);
$orderId = $_GET["id"];
$orderInfo = Order::GetById($orderId);
$clientObject = Order::GetById($orderId)->GetClient();
$clientExtendPhoneNumber = substr_replace(substr_replace($clientObject->GetPhoneNumber(), '-' , 3,0),'-',7,0);

//select order status
$orderObject = Order::GetById($orderId);

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הזמנה: ".$_GET["id"]);
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "ordersBoardClass", "active");
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
                                <li><form method="POST" name="changeStatus" id="changeStatus" >
                                       <span>סטאטוס הזמנה:</span>
                                          <input type="hidden" name="SendEmail" id="SendEmail" value=0>
                                          <select id="orderstatus" name="orderstatus" required>
                                           {orderStatusEnum}
                                           </select>
                                    </form></li>
                                <li><span>עדכון אחרון:</span> {$orderInfo->GetUpdateTime()->format("d/m/y H:i")}</li>
                           </ul> 
                        <div class="btn btn-primary" style="float: left; margin: -34px 0 0 3px;" onclick="document.location ='editorder.php?orderId={$orderId}';">ערוך הזמנה </div>
                    </div>
                </div>
                <div class="col-sm-6" style="height: 250px;">
                    <div class="order-client-info">
                        <span><h4> פרטי לקוח </h4></span>
                             <ul>
                               <li><span> שם הלקוח:</span> {$orderInfo->GetClient()->GetFullName()}</li>
                               <li><span> פלאפון:</span> {$clientExtendPhoneNumber}</li>
                               <li><span> לקוח מעוניין בעדכון ע"י אימייל:</span>&nbsp;<span style="font-weight: normal" id="ClientWantEmails" data-value="{clientWantsEmailsBool}">{ClientWantsEmails}</span></li>
                               <li><span> אימייל:</span>    {$orderInfo->GetClient()->GetEmail()}</li>
                            </ul>
                         <div class="btn btn-primary" style="float: left; margin: 3px;" onclick="document.location ='editclient.php?clientId={$orderInfo->GetClient()->GetId()}';">ערוך לקוח </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="height: auto;">
                    <div class="order-products-info">
                        <span><h4> רשימת מוצרים 
                        <span class="btn btn-primary" onclick="document.location = 'addproduct.php?orderid={$orderId}';"> הוסף מוצר </span></h4></span>
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

$productRow = <<<EOF
<tr style="cursor: pointer;" onclick="document.location = 'editproduct.php?id={$orderId}&productId={productId}'">
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
    \Services::setPlaceHolder($productList, "productId",$product->GetId());

}
\Services::setPlaceHolder($PageTemplate, "productsList", $productList);

//set seller name - can be change or delete
try {
    $orderSeller = $orderObject->GetSeller()->GetFullName();
    \Services::setPlaceHolder($PageTemplate, "SellerName", $orderSeller);
} catch (\Exception $e) {
    $errorMsg = $e->getMessage();
    \Services::setPlaceHolder($PageTemplate, "SellerName", "מוכר לא ידוע");
}
///

///set order change status
$orderStatusString = "";
foreach (EOrderStatus::toArray() as $status) {
    $orderStatusString .= "<option value='".$status[0]."' ";
    if ($orderObject->GetStatus()->getValue() == $status[0]){
        $orderStatusString .= "selected='selected'";}
    $orderStatusString .= ">".$status[1]."</option>";

}
\Services::setPlaceHolder($PageTemplate, "orderStatusEnum", $orderStatusString);
/////


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
////


echo $PageTemplate;


if(isset($_POST['orderstatus'])) {
    $order_status = $_POST['orderstatus'];
    $clientWantEmail = $_POST['SendEmail'];

    //Log the status changing
    $oldStatus = EOrderStatus::search($order_status)->getDesc();
    $timeNow = new \DateTime( "now", new \DateTimeZone("Asia/Jerusalem"));
    $logFile = fopen("logs/StatusLog.php", "a");
    fwrite($logFile, "\n" . "<br>" . "{$timeNow->format("Y/m/d H:i:s")} - סניף <b>{$orderObject->GetShop()->GetShopName()}</b> - הזמנה מספר <b>{$orderObject->GetId()}</b> עברה מסטאטוס <b>{$orderObject->GetStatus()->getDesc()}</b> לסטאטוס <b>{$oldStatus}</b>.");
    fclose($logFile);

    //Update Status
    if(Order::GetById($orderId)->ChangeStatus($order_status)) {

        if ($clientObject->IsWantEmail() && $clientWantEmail == 1) {
                $subject = "הזמנתך בבאג מחכה לך בסניף {$orderObject->GetShop()->GetShopName()}";
                $message = Constant::EMAIL_CLIENT_ORDER_ARRIVED . "<img src='bug.845.co.il/Mailchecker.php/?orderId={$orderId}'>";
                \Services::setPlaceHolder($message,"Name", $clientObject->GetFullName());
                \Services::setPlaceHolder($message, "ShopName", $shopObject->GetShopName());

                try{
                    $clientObject->SendEmail($message, $subject);
                } catch (Exception $e) {
                    $errorMsg = $e->getMessage();
                    echo $errorMsg;
                    echo "לא ניתן לשלוח מייל";

                } catch (\Throwable $e) {
                    $errorMsg = $e->getMessage();
                    echo $errorMsg;
                    echo "לא ניתן לשלוח מייל";
                }
        }
    }

    header("Location: Ordersboard.php");
}

?>