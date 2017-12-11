<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 30-Oct-17
 * Time: 09:37
 */

namespace BugOrderSystem;

const headerTemplate = <<<Header
<!DOCTYPE html>
<html>
<head>
    <title>{PageTitle}</title>
    
        <!-- BootStrap -->
        <!-- Latest compiled and minified CSS -->
        <!--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css"></style>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!-- End BootStrap -->
    
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/rstyle.css">
    <link rel="stylesheet" type="text/css" href="../css/table.css">

    <!-- jquery Core -->
        <script
        src="https://code.jquery.com/jquery-3.2.1.min.js" 
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" 
        crossorigin="anonymous"></script>
        
     <!-- jquery ui -->
        <script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" 
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" 
        crossorigin="anonymous"></script>
        
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  
          <!-- Bootstrap 3 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <!--site main js-->
    <script src="../js/main.js"></script>
    <script src="../js/jqueryMain.js"></script>
    
    <meta name="viewport" content="width=device-width">
</head>

Header;

const headerMenu = <<<HeaderMenu
<body>
        <nav class="navbar navbar-default">
      <div class="container-fluid">        
          <logo>
              <img src="../images/logo.png" alt="logo">
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
              <div class="shop-details">שלום, {regionName}
                <br><a href="../logout.php"><img src="../images/icons/exit.png" alt="exit"></a>
              </div>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown {manageTools}"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-menu-down"></i> כלי ניהול <i class="glyphicon glyphicon-briefcase"></i></a>
                <ul class="dropdown-menu"  style="text-align: center">
                    <li><a href="addshop.php">הוספת סניף</a></li>
                    <li><a href="raddemployee.php">הוספת עובד</a></li>
                </ul>
            </li>
            <li class="{shopsPageClass}"><a href="rshops.php">סניפים <i class="glyphicon glyphicon-home"></i></a></li>
            <li class="{mainPageClass}"><a href="rindex.php">ראשי <i class="glyphicon glyphicon-list-alt"></i></a></li>
          </ul>
        </div>
      </div>
    </nav>
   
    <!--
              <header>

              <div class="wrapper">
                  <logo>
                      <img src="../images/logo.png" alt="logo">
                  </logo>
                  <div class="shop-details">שלום, {regionName}
                    <br><a href="../logout.php"><img src="../images/icons/exit.png" alt="exit"></a>
                  </div>
                  <nav>
                    <div id="burger-nav"><img src="../images/icons/burger.png"></div>
                      <h2>Main Navigation</h2>
                      <ul>
                         <li><a href="rshops.php" class={shopsPageClass}>סניפים</a></li>
                         <li><a href="rindex.php" class={mainPageClass}>ראשי</a></li>
                      </ul>
                  </nav>
                  </div>
              </header>
-->
HeaderMenu;

const footer = <<<Footer

</body>
<footer>
        <div id="footer"> ~Beta 2.0~ </div>
</footer>
</html>
Footer;


