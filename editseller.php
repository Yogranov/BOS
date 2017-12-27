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
$PageTemplate .= headerBody;
\Services::setPlaceHolder($PageTemplate, "HeaderMenu", headerMenu);
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "mainPageClass", "active");
///


$PageTemplate .= <<<PAGE

    <main>
        <div class="container">
            <div id="new-order">

                <form method="POST">
                    <center>עריכת מוכרן - {$sellerObject->GetId()}</center>
                
                    <div class="form-group">
                        <label for="form-seller-id">מספר עובד</label>
                        <input type="text" class="form-control" id="form-seller-id" name="sellernumber" value="{$sellerObject->GetId()}" readonly disabled><br>
                    </div>
                
                    <div class="form-group">
                        <label for="form-seller-first-name">שם פרטי</label>
                    <input type="text" class="form-control" name="firstname" id="form-seller-first-name" value="{$sellerObject->GetFirstName()}" disabled><br>
                    </div>
                
                
                    <div class="form-group">
                        <label for="form-seller-last-name">שם משפחה</label>
                    <input type="text" class="form-control" id="form-seller-last-name" name="lastname" value="{$sellerObject->GetLastName()}" disabled><br>
                    </div>
                
                    <div class="form-group">
                        <label for="form-seller-email">אימייל</label>
                    <input type="text" class="form-control" id="form-seller-email" name="email" value="{$sellerObject->GetEmail()}" required><br>
                    </div>
                
                    <input type="submit" value="עדכן מוכרן" name="editseller" class="btn btn-info btn-block">


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


    if(!empty($seller_email)) {
        try {
            Seller::GetById($sellerId)->ChangeEmail($seller_email);
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


