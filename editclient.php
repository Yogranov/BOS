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
\Services::setPlaceHolder($PageTemplate, "ordersBoardClass", "'current'");
///


$PageTemplate .= <<<PAGE
<main>
    <div class="wrapper">
        <div id="new-order">

            <form class="new-order" method="POST">
                <center>       {$clientObject->GetId()}  -  עריכת לקוח</center>
                <br>
                שם פרטי<br>
                <input type="text" name="firstname" value="{$clientObject->GetFirstName()}" required><br>
                שם משפחה<br>
                <input type="text" name="lastname" value="{$clientObject->GetLastName()}"  required><br>
                פלאפון<br>
                <input type="text" name="phonenumber" value="{$clientObject->GetPhoneNumber()}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                <br>
                מעוניין לקבל עדכונים במייל <input type="checkbox" id="checkwantsemails" name="wantsemail" style="display = 'none'" onclick="emailsClick()" {checkedString}><br><br>
                <div id="clientwantsemails" class="{emailClassString}">
                :אימייל
                <input type="text" name="email" value="{$clientObject->GetEmail()}"><br>
                </div>
                <br>
                <button type="submit" name="editclient">עדכן לקוח</button>
                <br>
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

    if (!isset($_POST['wantsemail'])) {
        $client_wants_emails = 0;
    } else {
        $client_wants_emails = 1;
    }

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
                $clientObject->$func($attr);
            }
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


