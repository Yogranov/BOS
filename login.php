<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:57
 */
namespace BugOrderSystem;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

require_once "Classes/BugOrderSystem.php";

$errorMsg = "";

LoginC::Reconnect($_SESSION, "index.php");

if(isset($_POST['login'])) {
    $username = stripslashes(strip_tags($_POST['username']));
    $password = md5(stripslashes(strip_tags($_POST['password'])));
    @$remember = ($_POST['remember']) ? true : false;

    try{
        new LoginC($username,$password, $remember);
        header("Location: index.php");
    } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
    }
}

$html = <<<EOF
<!DOCTYPE html>
<html>
<head>
    <title>Login </title>
    
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">

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
        <!-- Bootstrap 3 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  
    <!--site main js-->
    <script src="js/main.js"></script>
    <script src="js/jqueryMain.js"></script>
    
    <meta name="viewport" content="width=device-width">
</head>
<body>
    <nav class="navbar navbar-default">
      <div class="container-fluid">
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
          <ul class="nav navbar-nav navbar-right">
            <li><a href="about.php">אודות <i class="glyphicon glyphicon-comment"></i></a></li>
            <li><a href="login.php">כניסה <i class="glyphicon glyphicon-log-in"></i></a></li>
          </ul>
        </div>
      </div>
    </nav>

<!--
    <header>
       <div class="wrapper">
          <logo>
              <img src="images/logo.png" alt="logo">
          </logo>
          <nav>
           <div id="burger-nav"><img src="images/icons/burger.png" alt="exit"></div>
           <h2>Main Navigation</h2>
              <ul>
                  <li><a href="about.php">אודות</a></li>
                  <li><a href="index.php"  class="current">כניסה</a></li>
              </ul>
          </nav>
      </div>
    </header>       
    -->
    <main>
        <div class="wrapper">
            <div id="store-logo"><img src="images/store.png"></div>
            <div id="login">
                <div class="login-form-top">
                    <span class="Warning">{$errorMsg}</span>    
                    <form class="login-form" method="POST">
                         :שם הסניף<br>
                         <input type="text" name="username" required>
                         <br>
                          :סיסמה<br>
                         <input type="password" name="password" required>
                     <br>
                     </div>
                    <div class="login-form-bottom">
                        <label for="remember"><span class="login-form-remember">זכור אותי</span></label>
                        <input type="checkbox"  style="width: 15px; height: 15px; cursor: pointer; position: relative;" id="remember" name="remember">
                        <input type='submit' value='' name='login'>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>


<footer>

</footer>


</html>
EOF;

echo $html;
