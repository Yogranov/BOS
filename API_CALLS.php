<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 04-Nov-17
 * Time: 16:07
 */
namespace BugOrderSystem;
header('Access-Control-Allow-Origin: *');

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

require_once("Classes/Services.php");
require_once("Classes/BugOrderSystem.php");

/* TODO: Need to work on the link encoding!
$AllowedIps = array("88.99.28.98");
\Services::dump($_SERVER);
$found = @array_search($_SERVER['REMOTE_ADDR'], $AllowedIps,True);
if (!$found) {
    Die();
}
*/

$pageMethod = html_entity_decode($_REQUEST['method']);
$pageData = \Services::MultiToArray(html_entity_decode($_REQUEST['data']), "|");
$JqAutoCompleteTerm = $_REQUEST['term'];
unset($_REQUEST);

$outputData = array();
try {
    switch ($pageMethod) {
        case 'GetChart':
            list($chartType, , $ClassName, $id, $cut, $sinceDate) = $pageData;
            $shopClass = &Shop::GetById($id);
            $since = new \DateTime($sinceDate);
            switch ($cut) {
                case 'CancelledOrders':
                    $innerData = Order::GetCanceledOrders($shopClass, $since);
                    break;

                case 'ActiveOrders':
                default:
                    $innerData = Order::GetActiveOrders($shopClass);
            }

            // do not touch below!
            $orderCount = array();
            foreach ($innerData as $orderObject) {
                $orderCount[$orderObject->GetStatus()->getValue()]++;
            }
            $outputData = array(
                array("Column", "Value")
            );
            foreach ($orderCount as $countStatus => $count) {
                $outputData[] = array(EOrderStatus::search($countStatus)->getDesc(), $count);
            }
            break;

        case "OrderInformClient":
            list($orderId) = $pageData;
            $orderObject = &Order::GetById($orderId);

            $subject = "הזמנתך בבאג מחכה לך בסניף {$orderObject->GetShop()->GetShopName()}";
            $serverLoc = Constant::SYSTEM_DOMAIN.Constant::SYSTEM_SUBFOLDER;
            $message = Constant::EMAIL_CLIENT_ORDER_ARRIVED . "<img src='{$serverLoc}Mailchecker.php/?orderId={$orderId}'>";
            \Services::setPlaceHolder($message, "Name", $orderObject->GetClient()->GetFullName());
            \Services::setPlaceHolder($message, "ShopName", $orderObject->GetShop()->GetShopName());

            try {
                $orderObject->GetClient()->SendEmail($message, $subject);
                $outputData[] = "success";
            } catch (\Throwable $e) {
                $errorMsg = $e->getMessage();
                $outputData = "לא ניתן לשלוח מייל ".$errorMsg;
            }
            break;

        case "SearchProductByBarcode":
            if (isset($JqAutoCompleteTerm))
                $productBacrcode = $JqAutoCompleteTerm;
            else
                list($productBacrcode) = $pageData;

            $data = BugOrderSystem::GetDB()->where('Barcode', "^".$productBacrcode."*", "REGEXP")->get("products", null, "Barcode, Name");
            if (BugOrderSystem::GetDB()->count > 0) {
                foreach ($data as $product) {
                    array_push($outputData, array("label" => $product["Barcode"].": ".$product["Name"], "value" => $product["Barcode"]));
                }
            }
            else
                $outputData = 0;
            break;

        case "GetProductData":
            list($productBacrcode, $location) = $pageData;
            try {
                $productObject = &Products::GetByBarcode($productBacrcode);
                switch ($location) {
                    case "javascript":
                        $outputData["Barcode"] = $productObject->GetBarcode();
                        $outputData["Name"] = $productObject->GetName();
                        $outputData["Remark"] = $productObject->GetRemark();
                        break;

                    default:
                        $outputData = serialize($productObject);
                }

            }
            catch (\Throwable $e) {
                $outputData = 0;
            }
            break;

        case "InsertNewProduct":
            list($productBacrcode, $productName, $productRemark) = $pageData;
            try {
                $productObject = &Products::Add($productBacrcode, $productName, $productRemark);
                $outputData = serialize($productObject);
            }
            catch (\Throwable $e) {
                $outputData = 0;
            }
            break;

        default:
            throw new Exception("invalid API '%1' method!", $pageData, $pageMethod);
    }
} catch (\Throwable $e) {
    $outputData = $e->getMessage();
}

echo json_encode($outputData);