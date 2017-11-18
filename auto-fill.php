<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 26-Sep-17
 * Time: 23:22
 */
namespace BugOrderSystem;

require_once "Classes/BugOrderSystem.php";


$phoneNumber = $_GET["phoneNumber"];
$sql = BugOrderSystem::GetDB()->where("PhoneNumber",$phoneNumber)->getOne("clients");

$data = array(
    'FirstName' => $sql['FirstName'],
    'LastName' => $sql['LastName'],
    'Email' => $sql['Email']
);

echo json_encode($data);

?>