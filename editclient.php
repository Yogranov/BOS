<?php
/**
 * Created by PhpStorm.
 * User: YogevAgranov
 * Date: 26/09/2017
 * Time: 19:44
 */
namespace BugOrderSystem;

session_start();
require_once "Classes/BugOrderSystem.php";

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if ($_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
$clientId = $_GET["clientId"];
$clientObject = &Client::GetById($clientId);

//setting header
require_once "Header.php";


$pageBody = <<<PAGE
<main>
    <div class="container">
        <div id="new-order">

            <form method="POST">
                <center>עריכת לקוח - {$clientObject->GetId()}</center>
                <br>
                <div class="form-group">
                    <label for="client-first-name">שם פרטי</label>
                    <input type="text" class="form-control" name="firstname" id="client-first-name" value="{$clientObject->GetFirstName()}" required><br>
                </div>
                
                <div class="form-group">
                    <label for="client-last-name">שם משפחה</label>
                <input type="text" class="form-control" id="client-last-name" name="lastname" value="{$clientObject->GetLastName()}"  required><br>
                </div>
                
                <div class="form-group">
                    <label for="client-phone-number">מספר טלפון</label>
                <input type="text" class="form-control" name="phonenumber" id="client-phone-number" value="{$clientObject->GetPhoneNumber()}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                </div>

                <div class="form-group">
                    <label for="client-email">אימייל</label>
                <input type="text" class="form-control" id="client-email" name="email" value="{$clientObject->GetEmail()}"><br>
                </div>
                
                <input type="submit" value="עדכן לקוח" name="editclient" class="btn btn-info btn-block">

            </form>

        </div>
    </div>
</main>
PAGE;


//Take form filed and make them variable.

if(isset($_POST['editclient'])) {

    $arrayToUpdate = array(
        "SetFirstName" => $_POST['firstname'],
        "SetLastName" => $_POST['lastname'],
        "SetPhoneNumber" => $_POST['phonenumber'],
        "ChangeEmail" => $_POST['email']
    );

    if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['phonenumber'])) {
        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $clientObject->$func($attr, false);
            }
            $clientObject->Update();

            if ((is_bool($_GET["ShowHeaderFooter"]) && !$_GET["ShowHeaderFooter"]) || !isset($_GET["ShowHeaderFooter"]))
                header("Location: ".$_SESSION["REFERER"]);
            else
                echo "<script>window.location.href = '{$_SESSION["REFERER"]}';</script>";

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות המבוקשים";
    }
}

\Services::setPlaceHolder($GLOBALS["PageTemplate"], "PageBody", $pageBody);
echo $GLOBALS["PageTemplate"];
?>


