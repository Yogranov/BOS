<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:40
 */

namespace BugOrderSystem;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "Classes/BugOrderSystem.php";
use Log\ELogLevel;
use Log\Message;

/*
$productData = array(
    "8806088999920" => array(1, 1, "blablabla"),
    "7290008314299" => array(2, 4, "")
);
var_dump(json_encode($productData));
*/

/*
$orderObject = &Order::GetById("161");
\Services::dump($orderObject);
//\Services::dump($_SERVER);
*/

try
{
    BugOrderSystem::GetLog();
    $SearchArray = array("הזמנה 201");
    \Services::dump(Message::SearchMessage(BugOrderSystem::$logReadHandlers["db"], $SearchArray));
    /*
    foreach (BugOrderSystem::$logReadHandlers as $where => $method) {
        \Services::dump($where);
        \Services::dump($method->Read());
    }
    */
}
catch (\Throwable $e) {
    echo $e->getMessage();
    BugOrderSystem::GetLog()->LogException($e);
}

/*
\Services::dump(EOrderStatus::Arrived[0]);

$orderId = 130;
$orderObject = &Order::GetById($orderId);
\Services::dump($orderObject);


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


*/