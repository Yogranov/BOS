<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 11-Dec-17
 * Time: 10:29
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
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הוספת עובד");
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
            <center>הוסף עובד חדש</center>
            
            <div class="form-group">
                <label for="form-employee-id">מספר עובד</label>
                <input type="text" class="form-control" id="form-employee-id" placeholder="מספר עובד" name="employeeId" required>
            </div>
            
            <div class="form-group">
                <label for="form-employee-first-name">שם פרטי</label>
               <input type="text" class="form-control" id="form-employee-first-name" placeholder="שם פרטי" name="employeeFirstName" required>
            </div>
            
            <div class="form-group">
                <label for="form-employee-last-name">שם משפחה</label>
               <input type="text" class="form-control" id="form-employee-last-name" placeholder="שם משפחה" name="employeeLastName" required>
            </div>
            
            <div class="form-group">
                <label for="form-employee-shop">סניף</label>
                <select  class="form-control" id="form-employee-shop" name="employeeShop">
                {shopSelect}
                </select>
            </div>
            
            <div class="form-group">
                <label for="form-employee-email">אימייל</label>
                <input type="text" class="form-control" id="form-employee-email" placeholder="אימייל" name="employeeEmail" required>
            </div>
            
            <input type="submit" value="צור עובד חדש" name="addEmployee" class="btn btn-info btn-block">
            </form>
        </div>
    </div>
</main>
INDEX;

//setting footer
$PageTemplate .= footer;


$allShops = BugOrderSystem::GetDB()->get("shops",null,["Id,Name"]);
$shopString = "";
foreach ($allShops as $shop) {
    $shopString .= "<option value='".$shop["Id"]."' ";
    $shopString .= ">".$shop["Name"]."</option>";
}
\Services::setPlaceHolder($PageTemplate, "shopSelect", $shopString);
/////


///////Add a new shop
if(isset($_POST['addEmployee'])) {

    $employeeId = $_POST['employeeId'];
    $employeeFirstName = $_POST['employeeFirstName'];
    $employeeLastName = $_POST['employeeLastName'];
    $employeeShop = $_POST['employeeShop'];
    $employeeEmail = $_POST['employeeEmail'];

    if(!empty($employeeId) && !empty($employeeFirstName) && !empty($employeeLastName) && !empty($employeeShop) && !empty($employeeEmail)) {
            try {
                $newEmployee = Seller::Add(array(
                    "Id" => $employeeId,
                    "FirstName" => $employeeFirstName,
                    "LastName" => $employeeLastName,
                    "ShopId" => $employeeShop,
                    "Email" => $employeeEmail));
                if ($newEmployee) {
                    header('location: rindex.php');
                    exit;
                } else {
                    echo "לא ניתן להוסיף מוכרן כרגע";
                }
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
                echo $errorMsg;
            }
    } else {
        echo "נא למלא את כל הפרטים";
    }
}
////////

echo $PageTemplate;

