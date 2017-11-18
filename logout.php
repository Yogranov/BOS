<?php
namespace BugOrderSystem;

require_once "Classes/BugOrderSystem.php";

session_start();

/*
BugOrderSystem::GetDB()->where("UserId",$_SESSION["ShopId"])->delete("cookies",1);
Cookie::Delete("ShopCookie");
*/

LoginC::Disconnect($_SESSION);

header('Location: login.php');
?>