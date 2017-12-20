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

//echo 12344;
//\Services::dump(new \DateTime("1512506414"));
try
{
    BugOrderSystem::GetLog();
    //BugOrderSystem::GetLog()->Write("BlaBla2");
    foreach(BugOrderSystem::$logReadHandlers as $where => $handler) {
        \Services::dump($where);
        \Services::dump($handler->Read(0, new \DateTime("2017-12-02 14:35")));
    }
    //$data = &Region::GetById(0);
}
catch (\Throwable $e) {
    echo $e->getMessage();
    BugOrderSystem::GetLog()->Write($e->getMessage(), ELogLevel::ERROR(), debug_backtrace());
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