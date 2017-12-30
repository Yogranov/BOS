<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:40
 */
namespace BugOrderSystem;
require_once "Classes/BugOrderSystem.php";
use Log\ELogLevel;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$orderObject = Order::GetById(137);

$encode = base64_encode($orderObject->GetShop()->GetId() . "_" . $orderObject->GetId() . "_" . $orderObject->GetTimeStamp()->format("U"));

echo $encode;













/*
$orderObject = Order::GetById(84);
$clientId = 76;
$orderSummery = Constant::EMAIL_CLIENT_SUMMERY_ORDER;
$encode = base64_encode($orderObject->GetShop()->GetId() . "_" . $orderObject->GetId() . "_" . $orderObject->GetTimeStamp()->format("U"));

\Services::setPlaceHolder($orderSummery,"OrderId",$orderObject->GetId());
\Services::setPlaceHolder($orderSummery,"ClientName",$orderObject->GetClient()->GetFirstName());
\Services::setPlaceHolder($orderSummery,"StatusCheckURL", $encode);
\Services::setPlaceHolder($orderSummery,"OrderDate", $orderObject->GetTimeStamp()->format("d/m/y H:m"));
\Services::setPlaceHolder($orderSummery,"ShopName", $orderObject->GetShop()->GetShopName());
\Services::setPlaceHolder($orderSummery,"Address", $orderObject->GetShop()->GetLocation());
\Services::setPlaceHolder($orderSummery,"Seller", $orderObject->GetSeller()->GetFirstName());
\Services::setPlaceHolder($orderSummery,"PhoneNumber", $orderObject->GetShop()->GetPhoneNumber());

//set client object
$clientObj = Client::GetById($clientId);

echo $orderSummery;
//$clientObj->SendEmail($orderSummery,"סיכום הזמנה");




$order = Order::GetById(91);
$encode = base64_encode($order->GetShop()->GetId() . "_" . $order->GetId() . "_" . $order->GetTimeStamp()->format("U"));

$decode = base64_decode($encode);

\Services::dump(explode("_",$decode));


echo "<br><br><br><br>";

echo $encode;
echo "<br><br><br><br>";

echo $decode;




echo 12344;
try
{
    BugOrderSystem::GetLog();
    //BugOrderSystem::GetLog()->Write("BlaBla2");
    foreach(BugOrderSystem::$logReadHandlers as $handler) {
        \Services::dump($handler->Read(0, new \DateTime("2017-12-02 14:35")));
    }
    //$data = &Region::GetById(0);
}
catch (\Exception $e) {
    echo $e->getMessage();
    BugOrderSystem::GetLog()->Write($e->getMessage(), ELogLevel::ERROR(), debug_backtrace());
}



\Services::dump(EOrderStatus::Arrived[0]);

$orderId = 84;
$orderObject = Order::GetById($orderId);
$clientObject = $orderObject->GetClient();
$shopObject = $orderObject->GetShop();


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

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
            echo "לא ניתן לשלוח מייל";

}
/*

$hashCheck = BugOrderSystem::GetDB()->where("UserId", 94)->getOne("cookies","Hash");
$hashCheck = $hashCheck["Hash"];
\Services::dump($hashCheck);


$allSellers = Shop::GetById(94)->GetActiveSellers();
\Services::dump($allSellers);



$clientObject = Order::GetById(21)->GetClient();
\Services::dump(Order::GetById(21)->GetStatus()->getValue());

//Order::GetById(21)->ChangeStatus(2);


$datetime1 = new \DateTime(Order::GetById(23)->GetTimeStamp()->format("Y-m-d"));
\Services::dump(\Services::DateDiff($datetime1, "now", "'%a Day'"));




$datetime1 = new \DateTime(Order::GetById(17)->GetTimeStamp()->format("Y-m-d"));
$datetime2 = new \DateTime("now");
$interval = $datetime1->diff($datetime2);
echo $interval->format('%a');


echo "<br><br><br><br><br>";

Order::LoopAll(function(Order $order) {


    $datetime1 = new \DateTime($order->GetTimeStamp()->format("Y-m-d"));
    $datetime2 = new \DateTime("now");
    $interval = $datetime1->diff($datetime2);
    echo "הזמנה מספר - " . $order->GetId() . "<br>";
    echo $interval->format('%a') . "<br><br><br>";



});










$message = "זהו אימיי לבדיקה, נא לא להשיב למייל זה.";
$subject = "אימייל בדיקה!";

$client = Client::GetById(11);
$client->SendEmail($message, $subject, "images/logo.png");


mail("bug94@bug.co.il","זה מייל בדיקה","זה מייל בדיקה, נא לא להשיב.", 'From: BugOrderSystem <BugOrderSystem@bug.co.il>');

Seller::LoopAll(function(Seller $seller) {
    echo $seller->GetFullName()." (".$seller->GetEmail().")<br/>";
});

$message = "זהו אימיי לבדיקה, נא לא להשיב למייל זה.";
$subject = "אימייל בדיקה!";


foreach (Shop::GetById(94)->GetActiveSellers() as $seller) {
    $seller->SendEmail($message, $subject);
}


//Seller::GetById(1898)->SendEmail($message, $subject);



<!--

                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

                <form class="new-order" method="post">
                <center>הזמנה חדשה</center>
                <br>
                מספר טלפון<br>
                     <input type="text" name="phonenumber" id="PhoneNumber" pattern=".{10,}" maxlength="10" title="10 ספרות" onkeyup="this.value=this.value.replace(/[^\d]/,''); autofill();" required><br>
                שם פרטי<br>
                     <input type="text" name="firstname" id="FirstName" required><br>
                שם משפחה<br>
                     <input type="text" name="lastname" id="LastName" required><br>
                <label for="checkwantsemails"> מעוניין לקבל עדכונים במייל</label>
                     <input type="checkbox" id="checkwantsemails" name="wantsemail" style="cursor: pointer" onclick="emailsClick()" ><br><br>
                <div id="clientwantsemails">
                    אימייל<br>
                      <input type="text" name="email" id="Email">
                </div>


                הערות להזמנה<br>
                     <input type="text" name="remarks"><br>
                מוכרן<br>
                      <select  name="seller" required><br>
                         {sellerSelect}
                      </select>
                <br>
                <hr>
                <br>
                שם המוצר<br>
                      <input type="text" name="productname" required><br>
                ברקוד<br>
                      <input type="text" name="productbarcode" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                כמות<br>
                      <input type="text" name="quantity" value="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>

                <br>
                הערות למוצר
                      <input type="text" name="productremarks"><br>
                <br>
                      <button type="submit" name="neworder">צור הזמנה</button>

                <br>
            </form>
        </div>
    </div>
</main>
-->



*/