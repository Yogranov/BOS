<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 02-Nov-17
 * Time: 10:27
 */
namespace BugOrderSystem;
require_once "../Classes/BugOrderSystem.php";
session_start();

$regionId = $_SESSION["RegionId"];
if(!isset($regionId)) {
    header("Location: ../login.php");
}

$regionObj = Region::GetById($regionId);
$shopObj = Shop::GetById($_GET["id"]);

//setting header
require_once "Rheader.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "ראשי");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "regionName", $regionObj->GetManager()->GetFirstName());
\Services::setPlaceHolder($PageTemplate, "shopsPageClass", "active");
///


$PageTemplate .= <<<INDEX
<main>
    <div class="container">
        <div id="edit-shop">
            <form method="POST">
                <center>{$shopObj->GetId()}- עריכת חנות</center>
                
                <div class="form-group">
                    <label for="form-edit-shop-name">שם החנות</label>
                    <input type="text" class="form-control" id="form-edit-shop-name" name="ShopName" value="{$shopObj->GetShopName()}" required><br>
                </div>
                
                <div class="form-group">
                    <label for="form-edit-shop-address">כתובת</label>
                    <input type="text" class="form-control" id="form-edit-shop-address" name="ShopLocation" value="{$shopObj->GetLocation()}" required><br>
                </div>
                
                <div class="form-group">
                    <label for="form-edit-shop-phone-number">טלפון</label>
                    <input type="text" class="form-control" id="form-edit-shop-phone-number" name="shopPhoneNumber" value="{$shopObj->GetPhoneNumber()}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                </div>
                
                <div class="form-group">
                    <label for="form-edit-shop-email">אימייל</label>
                    <input type="text" class="form-control" id="form-edit-shop-email" name="shopEmail" value="{$shopObj->GetEmail()}"><br>
                </div>
                
                <div class="form-group">
                    <label for="form-edit-shop-manager">מנהל (מספר עובד)</label>
                    <input type="text" class="form-control" id="form-edit-shop-manager" name="shopManager" value="{$shopObj->GetManager()->GetId()}"><br>
                </div>
                
                <div class="form-group">
                    <label for="form-edit-shop-region">איזור</label>
                    <input type="text" class="form-control" id="form-edit-shop-region" name="shopRegion" value="{$shopObj->GetRegion()}"><br>
                </div>
                
                <input type="submit" value="עדכן חנות" name="editshop" class="btn btn-info btn-block">

                </form>
        </div>
    </div>  
    <script src="../js/chart.js"></script>

</main>
INDEX;


//setting footer
$PageTemplate .= footer;

if(isset($_POST['editshop'])) {

    $arrayToUpdate = array(
        "SetShopName" => $_POST['ShopName'],
        "SetLocation" => $_POST['ShopLocation'],
        "SetPhoneNumber" => $_POST['shopPhoneNumber'],
        "SetEmail" => $_POST['shopEmail'],
        "SetManager" => Seller::GetById($_POST['shopManager']),
        "SetRegion" => $_POST['shopRegion']
    );

    $checkManager = BugOrderSystem::GetDB()->where("Id",$_POST['shopManager'])->getOne(Seller::TABLE_NAME);

    if(!empty($_POST['ShopName']) && !empty($_POST['ShopLocation']) && !empty($_POST['shopPhoneNumber'])
            && !empty($_POST['shopEmail']) && !empty($_POST['shopRegion'])) {
        if (count($checkManager) != 0) {
            try {
                foreach ($arrayToUpdate as $func => $attr) {
                    $shopObj->$func($attr, false);
                }
                $shopObj->Update();
                header("Location: rshops.php");
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                echo $errorMsg;
            }
        } else {
            echo "מספר מנהל אינו תקין";
        }

    } else {
        echo "נא למלא את כל השדות המבוקשים";
    }
}



echo $PageTemplate;
