<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 24-Sep-17
 * Time: 20:30
 */
namespace BugOrderSystem;

const headerTemplate = <<<Header
<!DOCTYPE html>
<html>
<head>
    <title>{PageTitle}</title>
    <meta name="viewport" content="width=device-width">
    
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">


    <!-- jquery Core -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="  crossorigin="anonymous"></script>

    <!-- BootStrap 3 -->
    
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>



    <!-- BootStrap 4
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    -->
    <!-- jquery Core -->
        <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  
    <!-- BootStrap
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

    
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">

        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
     End BootStrap -->
    
    <!-- jquery ui -->
        
      <!--My Styles -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    
    <!--site main js-->
    <script src="js/main.js"></script>
    <script src="js/jqueryMain.js"></script>
    <script src="js/jqueryDialog.js"></script>
    <script src="js/jqueryAddProduct.js"></script>
    <script src="js/jqueryNewOrder.js"></script>
    <script src="js/jqueryViewOrder.js"></script>
</head>

Header;

const headerMenu = <<<HeaderMenu
        <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        
        <logo>
            <img src="images/logo.png" alt="logo">
        </logo>
        
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-2">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button> 
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-2">
            <div class="shop-details">שלום, {shopName}
                <br><a href="logout.php"><img src="images/icons/exit.png" alt="exit"></a>
          </div>
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
        </div>
      </div>
    </nav>
HeaderMenu;

const headerBody = <<<HeaderBody
<body>
    {HeaderMenu}
    <div id="BOS_Dialog">
        <iframe seamless='seamless'>
            <p>Your browser does not support iframes.</p>
        </iframe>
    </div>
    
    <!--
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
          </div>
          <div class="modal-body">
            <iframe seamless='seamless'>
                <p>Your browser does not support iframes.</p>
            </iframe>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    -->
</body>
HeaderBody;

const footer = <<<Footer
<footer>
        <div id="footer"> ~Test Beta 2.0~ </div>
</footer>
</html>
Footer;


