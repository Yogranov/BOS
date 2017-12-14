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

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
$clientId = $_GET["clientId"];
$clientObject = Client::GetById($clientId);
$shopObject = Shop::GetById($shopId);


//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "עריכת לקוח");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "ordersBoardClass", "active");
///


$PageTemplate .= <<<PAGE
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
                    <label for="client-first-name">שם פרטי</label>
                    <input type="text" class="form-control" name="firstname" id="client-first-name" value="{$clientObject->GetFirstName()}" required><br>
                </div>
                <label for="checkwantsemails">מעוניין לקבל עדכונים במייל</label>

                 <input type="checkbox" id="checkwantsemails" value=1 name="wantsemail" style="display = 'none'" onclick="emailsClick()" {checkedString}><br><br>

                <div id="clientwantsemails" class="{emailClassString}">
                <label for="client-wants-email">אימייל</label>
                <input type="text" class="form-control" id="client-wants-email" name="email" value="{$clientObject->GetEmail()}"><br>
                </div>
                
                <input type="submit" value="עדכן לקוח" name="editclient" class="btn btn-info btn-block">

                
                

            </form>

        </div>
    </div>
</main>
PAGE;

//setting footer
$PageTemplate .= footer;

$checkedString = "";
$emailClassString = "";
if ($clientObject->IsWantEmail()) {
    $checkedString = "Checked";
    $emailClassString = "open";
}
\Services::setPlaceHolder($PageTemplate,"checkedString", $checkedString);
\Services::setPlaceHolder($PageTemplate,"emailClassString", $emailClassString);


//Take form filed and make them variable.

if(isset($_POST['editclient'])) {

    $client_wants_emails = 0;
    if (isset($_POST['wantsemail']))
        $client_wants_emails = 1;

    $arrayToUpdate = array(
        "SetFirstName" => $_POST['firstname'],
        "SetLastName" => $_POST['lastname'],
        "SetPhoneNumber" => $_POST['phonenumber'],
        "SetWantEmail" => $client_wants_emails,
        "ChangeEmail" => $_POST['email']
    );

    if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['phonenumber'])) {
        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $clientObject->$func($attr, false);
            }
            $clientObject->Update();
            header("Location: Ordersboard.php");
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות המבוקשים";
    }
}

echo $PageTemplate;

?>


