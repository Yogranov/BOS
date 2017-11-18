<?php

/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:40
 */
namespace BugOrderSystem;

session_start();

require_once "Classes/BugOrderSystem.php";

$shopId = $_SESSION["ShopId"];
$regionId = $_SESSION["RegionId"];

if(!isset($shopId) && !isset($regionId)) {
    header("Location: login.php");
} elseif (isset($regionId)) {
    header("Location: region/rindex.php");
}

unset($_SESSION['manager']);


$passwordError = "";
$reminderError = "";

$managerPassword = Shop::GetById($shopId)->GetId();
$managerPassword .= "manager";
$shopObject = Shop::GetById($shopId);

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "ראשי");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "mainPageClass", "'current'");
///


$PageTemplate .= <<<INDEX
      <main>
        <div class="wrapper">
            <div id="manager-enter">כניסת מנהל
                <form method="post">
                    <input type="password" name="password" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                    <button type="submit" name="manager-entering">כניסה</button>
                </form>
                <br>
                <br>
                <br>
                <br>
                {passwordError}
            </div>
            
      <remindboard>
        <div class="table-users">
           <div class="header-users">לוח תזכורות</div>

            <table>
              <thead>
                <tr>
                  <th>מחיקה</th>
                  <th>מוכרן</th>
                  <th>תזכורת</th>
                  <th>תאריך</th>
                </tr>
              </thead>
              <tbody>
                {remindsBoard_Table_Template}
              </tbody>
            </table>
        
        
              <form method="POST">
              תזכורת -
                <input type="text" name="Remind" required>
                 מוכרן - 
                <select name="selleraddreminder">
                  {selectSellers}
                </select>&nbsp;
                <button type="submit" name="addremind">
                הוסף תזכורת 
                </button>
                {reminderError}
              </form> 
      
      </div>
      
             
    </div>
    </remindboard>
    </main>
INDEX;
//setting footer
$PageTemplate .= footer;

/////Set the list of all seller on remind board
$allSellers = Shop::GetById($shopId)->GetActiveSellers();
$sellerOrder = "";
foreach ($allSellers as $seller) {
    $sellerOrder .= "<option value='".$seller->GetId()."' ";
    $sellerOrder .= ">".$seller->GetFullName() . "</option>";

}
\Services::setPlaceHolder($PageTemplate, "selectSellers", $sellerOrder);
//////


/////Reminder board
$RemindBoard_Table_Temlplate = <<<EOF
<tr>
    <td>{delete}</td>
    <td>{seller}</td>
    <td>{remind}</td>
    <td>{remindTime}</td>
</tr>
EOF;

$shopReminds = Reminder::GetShopReminders($shopObject);

$remindsBoard = (count($shopReminds) > 0) ? "" : "<tr colspan='7'><div id='no-orders-available'>אין תזכירים </div></tr>";
foreach ($shopReminds as $remind) {
    $remindsBoard .= $RemindBoard_Table_Temlplate;
    \Services::setPlaceHolder($remindsBoard, "delete",'<div class="delete-reminder" onclick="document.location =\'index.php?deleteId=' . $remind->GetId() . '\'' .'"> <img src="images/icons/garbage.png"> </div>');
    \Services::setPlaceHolder($remindsBoard, "seller", $remind->GetSeller()->GetFullName());
    \Services::setPlaceHolder($remindsBoard, "remind", $remind->GetRemind());
    \Services::setPlaceHolder($remindsBoard, "remindTime", $remind->GetTimestamp()->format("d/m/Y H:i"));
}
\Services::setPlaceHolder($PageTemplate, "remindsBoard_Table_Template", $remindsBoard);
//////


//////Add remind
if(isset($_POST['addremind'])) {

    $remind = $_POST['Remind'];
    $sellerAdd = $_POST['selleraddreminder'];

    if(!empty($seller)) {
        if (Reminder::Add(array("Remind" => $remind, "Seller" => $sellerAdd, "Shop" => $shopId))) {
            header("Location: index.php");
        }
    } else {
        $reminderError = "נא לבחור מוכרן";
    }
}
//////

/// Delete remind
if(isset($_GET['deleteId'])) {

    $deleteId = $_GET['deleteId'];

    Reminder::GetById($deleteId)->Delete();
    header("Location: index.php");

}
/////

////Manager enter
if(isset($_POST['manager-entering'])) {
    $password = $_POST['password'];

    if(!empty($password)) {
        if ($password == $managerPassword) {
            $_SESSION["manager"] = Shop::GetById($shopId)->GetManager();
            header("Location: shopmanager.php");
        } else {
            $passwordError .= "סיסמה לא נכונה";
        }
    } else {
        $passwordError .= "נא להכניס סיסמה";
    }
}
//////


/////errors placers
\Services::setPlaceHolder($PageTemplate,"passwordError", $passwordError);
\Services::setPlaceHolder($PageTemplate,"reminderError", $reminderError);
////



echo $PageTemplate;




?>