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

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$localUrl = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
if ($_SERVER["HTTP_REFERER"] !== $localUrl)
    $_SESSION["REFERER"] = $_SERVER["HTTP_REFERER"];

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}


//setting header
require_once "Header.php";


$pageBody = <<<PAGE
<main>
    <div class="container">
        <div id="new-order">

            <form method="POST">
                <center>עריכת לקוח</center>
                <br>
                
                <div class="form-group">
                    <label for="edit-client-phone-number">מספר טלפון</label>
                <input type="text" class="form-control" name="edit-client-phone-number" id="edit-client-phone-number" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                </div>
                
                <div class="form-group">
                    <label for="edit-client-first-name">שם פרטי</label>
                    <input type="text" class="form-control" name="edit-client-first-name" id="edit-client-first-name" required><br>
                </div>
                
                <div class="form-group">
                    <label for="edit-client-last-name">שם משפחה</label>
                <input type="text" class="form-control" id="edit-client-last-name" name="edit-client-last-name" required><br>
                </div>

                <div class="form-group">
                    <label for="edit-client-email">אימייל</label>
                <input type="text" class="form-control" id="edit-client-email" name="edit-client-email"><br>
                </div>
                
                <input style="display: none" type="hidden" name="edit-client-id" id="edit-client-id">
                
                <input type="submit" value="עדכן לקוח" name="edit-client-submit" class="btn btn-info btn-block">

            </form>

        </div>
    </div>
</main>
PAGE;


//Take form filed and make them variable.

if(isset($_POST['edit-client-submit'])) {

    $clientObject = Client::GetById((int)$_POST['edit-client-id']);

    $arrayToUpdate = array(
        "SetFirstName" => $_POST['edit-client-first-name'],
        "SetLastName" => $_POST['edit-client-last-name'],
        "SetPhoneNumber" => $_POST['edit-client-phone-number'],
        "ChangeEmail" => $_POST['edit-client-email']
    );

    if(!empty($_POST['edit-client-first-name']) && !empty($_POST['edit-client-last-name']) && !empty($_POST['edit-client-phone-number'])) {
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


