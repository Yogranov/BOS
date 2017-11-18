<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 02-Nov-17
 * Time: 10:27
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
$shopObj = Shop::GetById($_GET["id"]);

//setting header
require_once "Rheader.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "ראשי");
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
                <center>{$shopObj->GetId()}- עריכת חנות</center>
                <br>
                שם החנות<br>
                <input type="text" name="ShopName" value="{$shopObj->GetShopName()}" required><br>
                כתובת<br>
                <input type="text" name="ShopLocation" value="{$shopObj->GetLocation()}" required><br>
                טלפון<br>
                <input type="text" name="shopPhoneNumber" value="{$shopObj->GetPhoneNumber()}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                אימייל<br>
                <input type="text" name="shopEmail" value="{$shopObj->GetEmail()}"><br>
                אזור<br>
                <input type="text" name="shopRegion" value="{$shopObj->GetRegion()}"><br>
                <br>
                <button type="submit" name="editorder">עדכן חנות</button>
    
                <br>
                </form>
        </div>
    </div>  
    <script src="../js/chart.js"></script>

</main>
INDEX;


//setting footer
$PageTemplate .= footer;

if(isset($_POST['editorder'])) {

    $arrayToUpdate = array(
        "SetShopName" => $_POST['ShopName'],
        "SetLocation" => $_POST['ShopLocation'],
        "SetPhoneNumber" => $_POST['shopPhoneNumber'],
        "SetEmail" => $_POST['shopEmail'],
        "SetRegion" => $_POST['shopRegion']
    );

    if(!empty($_POST['ShopName']) && !empty($_POST['ShopLocation']) && !empty($_POST['shopPhoneNumber'])
            && !empty($_POST['shopEmail']) && !empty($_POST['shopRegion'])) {
        try {
            foreach ($arrayToUpdate as $func => $attr) {
                $shopObj->$func($attr, false);
            }
            $shopObj->Update();
            header("Location: reditshop.php?id={$shopObj->GetId()}");
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל השדות המבוקשים";
    }
}



echo $PageTemplate;
