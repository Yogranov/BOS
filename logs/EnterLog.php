<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 12-Nov-17
 * Time: 14:06
 */

session_start();
$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" lang="heb" dir="rtl">
<style>
    body {
        background-color: #c4d2d6;
        font-size: 18px;
        letter-spacing: 1px;
        line-height: 22px;
        padding: 10px;
    }
</style>
<h1>לוג התחברות:</h1>
