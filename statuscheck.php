<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 03-Dec-17
 * Time: 11:56
 */
namespace BugOrderSystem;

require_once "Classes/BugOrderSystem.php";

if(isset($_GET["id"])){
    $decodeId = base64_decode($_GET["id"]);
    $idExplode = explode("_",$decodeId);
    $orderObj = Order::GetById((int)$idExplode[1]);
    $shopId = $idExplode[0];
    $unixTime = $idExplode[2];
} else {
    exit("לא ניתן להציג מידע, מספר הזמנה לא קיים");
}


$PageTemplate = <<<PAGE
<!DOCTYPE html>
<html lang=""heb">
<head>
<title>בדיקת סטאטוס</title>
<meta charset="UTF-8"
<meta name="viewport" content="width=device-width">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
 integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
 
<style>
body {
    background-color: #f1f1f1;
}
</style>
</head>

<body style="direction: rtl; text-align: right;">
        <div class="jumbotron" style="padding: 40px 0">
                <div class="container"><h1 class="page-header">הזמנה {$orderObj->GetId()}</h1>
            <h2><small>קבלת מידע אודות סטאטוס ההזמנה</small></h2></div>
        </div>
    <br>
    <div class="container" style="margin-top: -20px">
        שלום {$orderObj->GetClient()->GetFirstName()},<br>
        ההזמנה שביצעת בתאריך {$orderObj->GetTimeStamp()->format("d/m/Y")} בסניף {$orderObj->GetShop()->GetShopName()} נמצאת כרגע בסטאטוס: <b>{$orderObj->GetStatus()->getDesc()}</b>.<br>
        לפרטים נוספים או בכל שאלה ניתן ליצור קשר עם הסניף בטלפון מספר {$orderObj->GetShop()->GetPhoneNumber()}.
        <br>
        <br>
        לנוחותך, מצורף סיכום ההזמנה:
        <br>
        <br>
        <ul style="list-style-type: none; line-height: 15px">
              <li>  <b>סטאטוס הזמנה:</b> {$orderObj->GetStatus()->getDesc()} </li><br>
              <li>  <b>מספר הזמנה:</b> {$orderObj->GetId()} </li><br>
              <li>  <b>תאריך:</b> {$orderObj->GetTimeStamp()->format("d/m/y H:m")} </li><br>
              <li>  <b>שם המזמין:</b>  {$orderObj->GetClient()->GetFirstName()} </li><br>
              <li>  <b>סניף:</b>  {$orderObj->GetShop()->GetShopName()} </li><br>
              <li>  <b>כתובת:</b>  {$orderObj->GetShop()->GetLocation()} </li><br>
              <li>  <b>מוכר:</b>  {$orderObj->GetSeller()->GetFirstName()} </li><br>
              <li>  <b>טלפון לבירורים:</b> {$orderObj->GetShop()->GetPhoneNumber()} </li><br>
        </ul>
        <br>
        מוצרים:
        <br>
        <br>
          <table class="table table-striped" style="text-align: center">
                <thead class="thead-light">
                  <tr>
                    <th>שם המוצר</th>
                    <th>כמות</th>
                    <th>סטאטוס מוצר</th>
                  </tr>
                </thead>
                  <tbody>
                    {productsList}
                </tbody>
          </table>
        <br>
        <br>
        תודה רבה, <br>
        סניף {$orderObj->GetShop()->GetShopName()}<br>
        {$orderObj->GetShop()->GetLocation()}
        
        <br><br>
        {mobile}
    </div>
</body>
PAGE;

$productRow = <<<EOF
<tr class="{flashClass}" style="color: {rowClass} !important;">
    <td>{productName}</td>
    <td>{productQuantity}</td>
    <td>{productStatus}</td>
</tr>
EOF;


$productList = "";
foreach ($orderObj->GetOrderProducts() as $product) {
    $productList .= $productRow;
    if (array_key_exists($product->GetStatus()->getValue(),Constant::PRODUCTS_STATUS_STYLE)) {
        \Services::setPlaceHolder($productList, "rowClass", Constant::PRODUCTS_STATUS_STYLE[$product->GetStatus()->getValue()][0]);
        \Services::setPlaceHolder($productList, "flashClass", Constant::PRODUCTS_STATUS_STYLE[$product->GetStatus()->getValue()][1]);
    }
    else {
        \Services::setPlaceHolder($productList, "rowClass", Constant::PRODUCTS_STATUS_STYLE["default"][0]);
        \Services::setPlaceHolder($productList, "flashClass", Constant::PRODUCTS_STATUS_STYLE["default"][1]);
    }

    \Services::setPlaceHolder($productList, "productName", $product->getProductName());
    \Services::setPlaceHolder($productList, "productQuantity", $product->GetQuantity());
    \Services::setPlaceHolder($productList, "productStatus", $product->GetStatus()->getDesc());

}
\Services::setPlaceHolder($PageTemplate, "productsList", $productList);


//if the client use smartPhone

$mobile = <<<MOBILE
        לניווט וחיוג מהיר: <br>
        <a href='waze://?q={$orderObj->GetShop()->GetLocation()}'><img src='/images/icons/waze.png' alt='waze' height='50' width='50'></a> 
        <a href=\"tel:{$orderObj->GetShop()->GetPhoneNumber()}\"><img src='/images/icons/telephone.png' alt='telephone'  height='50' width='50'></a>
MOBILE;

if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])){
    \Services::setPlaceHolder($PageTemplate,"mobile", $mobile);

} else {
    \Services::setPlaceHolder($PageTemplate,"mobile", "");
}
///////////////////////



if($orderObj->GetShop()->GetId() == $shopId && $orderObj->GetTimeStamp()->format("U") == $unixTime && $orderObj->GetStatus()->getValue() !== EOrderStatus::Aborted[0]) {
    echo $PageTemplate;


    //Log the status changing
    $logText = "הלקוח {$orderObj->GetClient()->GetFullName()} צפה בהזמנה מספר {$orderObj->GetId()}";
    BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO());


} else {
    echo "<h2>אירע שגיאה, לא ניתן להציג דף זה</h2>";
}


?>