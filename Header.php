<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 24-Sep-17
 * Time: 20:30
 */
namespace BugOrderSystem;

$pageHeader = <<<Header
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
    <script src="js/jqueryAddEditProduct.js"></script>
    <script src="js/jqueryEditClient.js"></script>
</head>
Header;

$pageMenuTemplate = "";
$pageFooter = "";
if (!isset($_GET["ShowHeaderFooter"]) || $_GET["ShowHeaderFooter"] != "0") {

    //footer
    $systemName = Constant::SYSTEM_NAME;
    $systemVer = Constant::SYSTEM_VERSION;
    $pageFooter = <<<Footer
<footer>
        <div id="footer"> {$systemName} - Ver {$systemVer} </div>
</footer>
</html>
Footer;

    //User Menu
    //TODO: need to work on the user custom menu from external source
    $InnerUserMenu = "";
    require_once "Menus.php";

    $pageMenuTemplate = <<<HeaderMenu
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
            $InnerUserMenu
        </div>
      </div>
    </nav>
HeaderMenu;
}

$pageBody = <<<HeaderBody
<body>
    {$pageMenuTemplate}
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
    
    {PageBody}
</body>
HeaderBody;

$GLOBALS["PageTemplate"] = $pageHeader.$pageBody.$pageFooter;


