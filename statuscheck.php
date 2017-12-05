<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 03-Dec-17
 * Time: 11:56
 */
namespace BugOrderSystem;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        <div class="jumbotron">
                <div class="container"><h1 class="page-header">הזמנה {$orderObj->GetId()}</h1>
            <h2><small>קבלת מידע אודות סטאטוס ההזמנה</small></h2></div>
        </div>
    <div class="container">
    </div>
    <br>
    <div class="container">
        שלום {$orderObj->GetClient()->GetFirstName()},<br>
        ההזמנה שביצעת בתאריך {$orderObj->GetTimeStamp()->format("d/m/y")} בסניף {$orderObj->GetShop()->GetShopName()} נמצאת כרגע בסטאטוס: <b>{$orderObj->GetStatus()->getDesc()}</b>.<br>
        לפרטים נוספים או בכל שאלה ניתן ליצור קשר עם הסניף בטלפון מספר {$orderObj->GetShop()->GetPhoneNumber()}.
        <br>
        <br>
        לנוחותך, מצורף סיכום ההזמנה:
        <br>
        <br>
          <table class="table table-striped">
                <thead class="thead-light">
                  <tr>
                    <th>שם המוצר</th>
                    <th>כמות</th>
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
        {wazeIcon}
        <br>
        {callMe}
    </div>
</body>
PAGE;


$productRow = <<<EOF
<tr>
    <td>{productName}</td>
    <td>{productQuantity}</td>
</tr>
EOF;

$productList = "";
foreach ($orderObj->GetOrderProducts() as $product) {
    $productList .= $productRow;
    \Services::setPlaceHolder($productList, "productName", $product->getProductName());
    \Services::setPlaceHolder($productList, "productQuantity", $product->GetQuantity());
}
\Services::setPlaceHolder($PageTemplate, "productsList", $productList);

//if the client use smartPhone
if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])){
   \Services::setPlaceHolder($PageTemplate,"wazeIcon", "<a href='waze://?q={$orderObj->GetShop()->GetLocation()}'><img src=\"http://www.nirtours.co.il/webfiles/links/32/waze-hover_-_g.svg\"></a>");
   \Services::setPlaceHolder($PageTemplate,"callMe", "<a href=\"tel:{$orderObj->GetShop()->GetPhoneNumber()}\"><img src='https://holidayford.com/images/phone-icon.png'></a>");

} else {
    \Services::setPlaceHolder($PageTemplate,"wazeIcon", "");
    \Services::setPlaceHolder($PageTemplate,"callMe", "");

}




if($orderObj->GetShop()->GetId() == $shopId && $orderObj->GetTimeStamp()->format("U") == $unixTime) {
    echo $PageTemplate;
} else {
    echo "לא ניתן להציג דף זה, נא להכניס מספר הזמנה חוקי";
}


?>