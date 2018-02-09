<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Feb-18
 * Time: 16:46
 */


$InnerUserMenu = "";
switch ($_SESSION["UserType"]){
    case "Region":
    case "ShopManager":

    $InnerUserMenu = <<<InnerUserMEnu
      <ul class="nav navbar-nav navbar-right">
        <li class="{productManagment}"><a href="editproduct.php">ניהול פריט <i class="glyphicon glyphicon-inbox"></i></a></li>
        <li class="{oldOrdersClass}"><a href="oldordersboard.php">הזמנות ישנות <i class="glyphicon glyphicon-inbox"></i></a></li>
        <li class="{newOrdersClass}"><a href="neworder.php" >הזמנה חדשה <i class="glyphicon glyphicon-edit"></i></a></li>
        <!-- <li class="{newOrdersClass}" data-action="OpenBOSDialog" data-page="neworder.php" data-dialogTitle="פתיחת הזמנה חדשה" data-variables="ShowHeaderFooter=0"><a href="#">הזמנה חדשה <i class="glyphicon glyphicon-edit"></i></a></li>-->
        <li class="{ordersBoardClass}"><a href="Ordersboard.php">לוח הזמנות <i class="glyphicon glyphicon-list-alt"></i></a></li>
        <li class="{mainPageClass}"><a href="index.php">ראשי <i class="glyphicon glyphicon-home"></i></a></li>
        <li>
          <a class="btn btn-default btn-outline btn-circle collapsed"  data-toggle="collapse" href="#nav-collapse2" aria-expanded="false" aria-controls="nav-collapse2">כניסת מנהל</a>
        </li>
      </ul>
      <div class="collapse nav navbar-nav nav-collapse slide-down" id="nav-collapse2">
        <form class="navbar-form navbar-right form-inline" role="form" method="post" action="shopmanager.php">
          <button type="submit" class="btn btn-success">כניסה</button>
          <div class="form-group">
            <label class="sr-only" for="Password">סיסמה</label>
            <input type="password" name="manager-password" class="form-control" id="Password" placeholder="סיסמה" required />
          </div>
        </form>
      </div>
InnerUserMEnu;

        break;

    case "Shop":
    default:
    $InnerUserMenu = <<<InnerUserMEnu
      <ul class="nav navbar-nav navbar-right">
        <li class="{oldOrdersClass}"><a href="oldordersboard.php">הזמנות ישנות <i class="glyphicon glyphicon-inbox"></i></a></li>
        <li class="{newOrdersClass}"><a href="neworder.php" >הזמנה חדשה <i class="glyphicon glyphicon-edit"></i></a></li>
        <!-- <li class="{newOrdersClass}" data-action="OpenBOSDialog" data-page="neworder.php" data-dialogTitle="פתיחת הזמנה חדשה" data-variables="ShowHeaderFooter=0"><a href="#">הזמנה חדשה <i class="glyphicon glyphicon-edit"></i></a></li>-->
        <li class="{ordersBoardClass}"><a href="Ordersboard.php">לוח הזמנות <i class="glyphicon glyphicon-list-alt"></i></a></li>
        <li class="{mainPageClass}"><a href="index.php">ראשי <i class="glyphicon glyphicon-home"></i></a></li>
        <li>
          <a class="btn btn-default btn-outline btn-circle collapsed"  data-toggle="collapse" href="#nav-collapse2" aria-expanded="false" aria-controls="nav-collapse2">כניסת מנהל</a>
        </li>
      </ul>
      <div class="collapse nav navbar-nav nav-collapse slide-down" id="nav-collapse2">
        <form class="navbar-form navbar-right form-inline" role="form" method="post" action="shopmanager.php">
          <button type="submit" class="btn btn-success">כניסה</button>
          <div class="form-group">
            <label class="sr-only" for="Password">סיסמה</label>
            <input type="password" name="manager-password" class="form-control" id="Password" placeholder="סיסמה" required />
          </div>
        </form>
      </div>
InnerUserMEnu;
        break;
}
