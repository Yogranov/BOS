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
\Services::setPlaceHolder($PageTemplate, "shopsPageClass", "'current'");
///


$PageTemplate .= <<<INDEX
<main>
    <div class="wrapper">
       <div id="edit-shop">
        <form method="POST">
        <center>הוסף סניף חדש</center>
        <br>
        מספר סניף<br>
           <input type="text" name="shopId" required>
           
         <br>
        סיסמה<br>
           <input type="password" name="shopPassword" required>
           <br>
          שם הסניף <br>
           <input type="text" name="shopName" required>
           <br>
              כתובת<br>
           <input type="text" name="shopLocation" required>
           <br>
                 מספר טלפון<br>
           <input type="text" name="shopPhoneNumber" required>
           <br>
                 (מנהל(מס' עובד<br>
           <input type="text" name="shopManager" required>
           <br>
                 אימייל<br>
           <input type="text" name="shopEmail" required>
           <br>
                  <br>
                            <button type="submit" name="addShop">צור חנות</button>
        </form>
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

