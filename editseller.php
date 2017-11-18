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

$manager = $_SESSION["manager"];
if(!isset($manager)) {
    header("Location: index.php");
}

$sellerId = $_GET["sellerId"];
$sellerObject = Seller::GetById($sellerId);
$shopObject = Shop::GetById($shopId);

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "עריכת מוכר");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "mainPageClass", "'current'");
///


$PageTemplate .= <<<PAGE

    <main>
        <div class="wrapper">
            <div id="new-order">

                <form class="new-order" method="POST">
                    <center>       {$sellerObject->GetId()}  -  עריכת מוכרן</center>
                    מספר מוכרן<br>
                    <input type="text" name="sellernumber" value="{$sellerObject->GetId()}" required><br>
                    <br>
                    שם פרטי<br>
                    <input type="text" name="firstname" value="{$sellerObject->GetFirstName()}" required><br>
                    שם משפחה<br>
                    <input type="text" name="lastname" value="{$sellerObject->GetLastName()}" required><br>
                    אימייל<br>
                    <input type="text" name="email" value="{$sellerObject->GetEmail()}" required><br>
                    <button type="submit" name="editseller">עדכן מוכרן</button>

                    <br>
                </form>

            </div>
        </div>
    </main>

PAGE;
//setting footer
$PageTemplate .= footer;


//Take form filed and make them variable.

if(isset($_POST['editseller'])) {

    $seller_number = $_POST['sellernumber'];
    $first_name = $_POST['firstname'];
    $last_name = $_POST['lastname'];
    $seller_email = $_POST['email'];


    if(!empty($seller_number) && !empty($first_name) && !empty($last_name) && !empty($seller_email)) {
        try {
            Seller::GetById($sellerId)->Update(array("Id" => $seller_number, "FirstName" => $first_name, "LastName" => $last_name, "Email" => $seller_email));
            header("Location: shopmanager.php");
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות הדרושים!";
    }
}

echo $PageTemplate;

?>


