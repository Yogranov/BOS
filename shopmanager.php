<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 07-Oct-17
 * Time: 09:49
 */

namespace BugOrderSystem;

session_start();
require_once "Classes/BugOrderSystem.php";

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

/*
$manager = $_SESSION["manager"];
if(!isset($manager)) {
    header("Location: index.php");
}
*/

$shopObject = &Shop::GetById($shopId);

$managerPassword = $shopObject->GetId();
$managerPassword .= "manager";

//setting header
require_once "Header.php";

//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "לוח מנהל");
//setting menu bar
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($GLOBALS["PageTemplate"], "mainPageClass", "active");
///


$PageBody = <<<PAGE
        <main>
            <div class="wrapper">
                <div id="reminder-table">
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-5">
                        <button class="btn btn-primary" data-action="OpenBOSDialog" data-page="editproduct.php" data-dialogTitle="עריכת מוצרים" data-variables="ShowHeaderFooter=0">עריכת מוצרים</button>
                        <button class="btn btn-primary" data-action="OpenBOSDialog" data-page="editclient.php" data-dialogTitle="עריכת לקוחות" data-variables="ShowHeaderFooter=0">עריכת לקוחות</button>
                    </div>
                </div>
                               <table>
                          <thead>
                            <tr>
                                <th>לפטר</th>
                                <th>עריכה</th>
                                <th>סטאטוס</th>
                                <th>שם מלא</th>
                                <th>מספר עובד</th>
                            </tr>
                          </thead>
                          <tbody>
                            {Seller_Table_Temlplate}
                          </tbody>
                        </table>
                        <form class="new-remind-form" method="POST">
                            <center>הוסף מוכרן חדש</center>
                            <br>
                            :מספר מוכרן<br>
                            <input type="text" name="sellernum" required>  
                            <br>
                            :שם פרטי<br>
                            <input type="text" name="firstname" required>
                            <br>
                            :שם משפחה <br>
                            <input type="text" name="lastname" required>
                            <br>
                            :אימייל<br>
                            <input type="text" name="email" required>
                            <br>
                            <br>
                            <button type="submit" name="addseller">הוסף מוכרן</button>
                        </form>
                    </div>
                </div>
   
</main>
PAGE;


//////////////////////Create Table Of All Sellers
$Seller_Table_Temlplate = <<<EOF
<tr>
    <td>{delete}</td>
    <td>{edit}</td>
    <td>{sellerStatus}</td>
    <td>{sellerName}</td>
    <td>{sellerNum}</td>
</tr>
EOF;


$allSellers = $shopObject->GetSellers();

$allSellerList = (count($allSellers) > 0) ? "" : "<tr colspan='7'><div id='no-orders-available'>אין מוכרנים </div></tr>";
foreach ($allSellers as $seller) {
    $allSellerList .= $Seller_Table_Temlplate;
    \Services::setPlaceHolder($allSellerList, "delete",'<div class="delete-reminder" onclick="document.location =\'shopmanager.php?deleteId=' . $seller->GetId() . '\'' .'"> פטר/החזר</div>');
    \Services::setPlaceHolder($allSellerList, "edit",'<div class="delete-reminder" onclick="document.location =\'editseller.php?sellerId=' . $seller->GetId() . '\'' .'"> ערוך</div>');
    \Services::setPlaceHolder($allSellerList, "sellerStatus", $seller->GetStatus()->getDesc());
    \Services::setPlaceHolder($allSellerList, "sellerName", $seller->GetFullName());
    \Services::setPlaceHolder($allSellerList, "sellerNum", $seller->GetId());

}
\Services::setPlaceHolder($PageBody, "Seller_Table_Temlplate", $allSellerList);
//////////////////////////////////////////






///////Fire/BackToWord Button
if(isset($_GET['deleteId'])) {

    $deleteId = $_GET['deleteId'];

    $sellerObj = &Seller::GetById($deleteId);
    try {
        if ($sellerObj->GetStatus()->getValue() == 1) {
            $sellerObj->Fire();
        } elseif ($sellerObj->GetStatus()->getValue() == 2) {
            $sellerObj->BackToWork();
        }

        header("Location: shopmanager.php");
    } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
        echo $errorMsg;
    }
}
////////





///////Add a new seller
if(isset($_POST['addseller'])) {

    $sellerNum = $_POST['sellernum'];
    $sellerFirstName = $_POST['firstname'];
    $sellerLastName = $_POST['lastname'];
    $sellerEmail = $_POST['email'];

    if(!empty($sellerNum) && !empty($sellerFirstName) && !empty($sellerLastName) && !empty($sellerEmail)) {
        try {
            $newSeller = Seller::Add(array(
                "Id" => $sellerNum,
                "FirstName" => $sellerFirstName,
                "LastName" => $sellerLastName,
                "ShopId" => $shopId,
                "Email" => $sellerEmail));
            if ($newSeller) {
                header('location: shopmanager.php');
                exit;
            } else {
                echo "לא ניתן להוסיף מוכרן כרגע";
            }
        } catch (\Exception $e){
            $errorMsg = $e->getMessage();
            echo $errorMsg;
        }
    } else {
        echo "נא למלא את כל הפרטים";
    }
}
////////


if(isset($_POST["manager-password"])) {
    $password = $_POST["manager-password"];
    $_SESSION["UserType"] = "ShopManager";
}

if(isset($_SESSION["UserType"]) && $_SESSION["UserType"] == "ShopManager") {
    \Services::setPlaceHolder($GLOBALS["PageTemplate"],"PageBody", $PageBody);
    echo $GLOBALS["PageTemplate"];

    } else {
    echo "שגיאה, נא נסו מאוחר יותר";
}











?>