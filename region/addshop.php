<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 23-Nov-17
 * Time: 10:21
 */
namespace BugOrderSystem;
require_once "../Classes/BugOrderSystem.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$regionId = $_SESSION["RegionId"];
if(!isset($regionId)) {
    header("Location: ../login.php");
}

$regionObj = Region::GetById($regionId);

//setting header
require_once "Rheader.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הוספת חנות");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "regionName", $regionObj->GetManager()->GetFirstName());
\Services::setPlaceHolder($PageTemplate, "manageTools", "active");
///


$PageTemplate .= <<<INDEX
<main>
    <div class="container">
       <div id="edit-shop">
            <form method="POST">
            <center>הוסף סניף חדש</center>
            
            <div class="form-group">
                <label for="form-shop-id">מספר סניף</label>
                <input type="text" class="form-control" id="form-shop-id" name="shopId" required>
            </div>
            
            <div class="form-group">
                <label for="form-shop-password">סיסמה</label>
               <input type="password" class="form-control" id="form-shop-password" name="shopPassword" required>
            </div>
            
            <div class="form-group">
                <label for="form-shop-name">שם הסניף</label>
               <input type="text" class="form-control" id="form-shop-name" name="shopName" required>
            </div>
            
            <div class="form-group">
                <label for="form-shop-address">כתובת</label>
               <input type="text" class="form-control" id="form-shop-address" name="shopLocation" required>
            </div>
            
            <div class="form-group">
                <label for="form-shop-phone-number">מספר טלפון</label>
                <input type="text" class="form-control" id="form-shop-phone-number" name="shopPhoneNumber" required>
            </div>
            
            <div class="form-group">
                <label for="form-shop-manager-id">מנהל (מספר עובד)</label>
                <input type="text" class="form-control" id="form-shop-manager-id" name="shopManager" required>
            </div>
            
            <div class="form-group">
                <label for="form-shop-email">אימייל חנות</label>
                <input type="text" class="form-control" id="form-shop-email" name="shopEmail" required>
            </div>
            
            <input type="submit" value="צור חנות" name="addShop" class="btn btn-info btn-block">
            </form>
        </div>
    </div>
</main>
INDEX;

//setting footer
$PageTemplate .= footer;



///////Add a new shop
if(isset($_POST['addShop'])) {

    $shopId = $_POST['shopId'];
    $shopPassword = md5($_POST['shopPassword']);
    $shopName = $_POST['shopName'];
    $shopLocation = $_POST['shopLocation'];
    $shopPhoneNumber = $_POST['shopPhoneNumber'];
    $shopManager = $_POST['shopManager'];
    $shopEmail = $_POST['shopEmail'];

    $checkSellerExist = BugOrderSystem::GetDB()->where("Id", $shopManager)->getOne("sellers");

    if(!empty($shopId) && !empty($shopPassword) && !empty($shopName) && !empty($shopLocation) && !empty($shopPhoneNumber)
        && !empty($shopManager) && !empty($shopEmail)) {
        if(count($checkSellerExist) != 0) {
            try {
                $newShop = Shop::AddShop(array(
                    "Id" => $shopId,
                    "Password" => $shopPassword,
                    "Name" => $shopName,
                    "Location" => $shopLocation,
                    "PhoneNumber" => $shopPhoneNumber,
                    "Manager" => $shopManager,
                    "Email" => $shopEmail,
                    "Region" => $regionId));
                if ($newShop) {
                    header('location: rindex.php');
                    exit;
                } else {
                    echo "לא ניתן להוסיף מוכרן כרגע";
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                echo $errorMsg;
            }
        } else {
            echo "מספר מנהל לא תקין";
        }
    } else {
        echo "נא למלא את כל הפרטים";
    }
}
////////

echo $PageTemplate;

