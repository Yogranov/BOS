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

if(isset($_GET["orderid"])){
    $orderObj = Order::GetById($_GET["orderid"]);
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
    <div class="container">
        <h1 class="page-header">בדיקת סטאטוס</h1>
        <h2><small>קבלת מידע אודות מיקום ההזמנה</small></h2>
    </div>
    <br>
    <div class="container">
      <b>מספר הזמנה:</b> {$_GET["orderid"]}<br>
      <b>סטאטוס הזמנה:</b> {$orderObj->GetStatus()->getDesc()}
      <br>
      <br>
      ההזמנה בוצעה בסניף {$orderObj->GetShop()->GetShopName()}, טלפון לבירורים: {$orderObj->GetShop()->GetPhoneNumber()}
    </div>
</body>

PAGE;



echo $PageTemplate;

?>